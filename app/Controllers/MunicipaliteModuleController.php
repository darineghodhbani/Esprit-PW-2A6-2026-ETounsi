<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/DemandeMunicipale.php';
require_once __DIR__ . '/../Models/MunicipaliteService.php';
require_once __DIR__ . '/../Models/RendezVous.php';
require_once __DIR__ . '/../Models/PaiementMunicipal.php';
require_once __DIR__ . '/../Models/NotificationMunicipale.php';

/** Module Municipalité intégré au compte et au thème communs E-Tounsi. */
class MunicipaliteModuleController extends Controller {
    private function db(): PDO { return Database::getConnection(); }
    private function citoyenId(): int { return (int)($_SESSION['id_citoyen'] ?? 0); }
    private function redirect(string $page, array $query=[]): never { header('Location: index.php?' . http_build_query(['page'=>$page]+$query)); exit; }
    private function flash(string $type,string $message): void { $_SESSION['flash']=['type'=>$type,'message'=>$message]; }
    public static function hasMunicipalitePermission(int $id, string $permission): bool {
        if ($id < 1 || !in_array($permission, ['consulter_demandes','traiter_demandes'], true)) return false;
        $st=Database::getConnection()->prepare('SELECT '.$permission.' FROM municipalite_agent_access a JOIN citoyen c ON c.id_citoyen=a.id_citoyen WHERE a.id_citoyen=? AND c.role="agent" AND c.actif=1');
        $st->execute([$id]); return (int)$st->fetchColumn()===1;
    }
    private function requireMunicipalitePermission(string $permission): void {
        $user=exigerConnexion();
        if (($user['role']??'')==='admin') return;
        if (($user['role']??'')==='agent' && self::hasMunicipalitePermission($this->citoyenId(),$permission)) return;
        auditEvenement('ACCESS_DENIED','Accès refusé au module Municipalité : permission '.$permission.'.',$this->citoyenId());
        http_response_code(403);
        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Accès refusé · E-Tounsi</title><main><h1>Accès refusé</h1><p>Votre compte ne possède pas l’autorisation nécessaire pour cette fonction municipale.</p><a href="index.php?page=home">Revenir à l’accueil</a></main></html>';
        exit;
    }
    private function phone(string $raw): string {
        if (!preg_match('/^\+?[0-9 () .-]+$/',trim($raw))) return '';
        $digits=preg_replace('/\D/','',$raw)??'';
        if (str_starts_with($digits,'216') && strlen($digits)===11) $digits=substr($digits,3);
        return preg_match('/^[2-9][0-9]{7}$/',$digits)?$digits:'';
    }
    private function details(string $type,array $p): array {
        $fields=match($type) {
            'naissance'=>['nom','prenom','date_naissance','lieu_naissance','sexe','pere_nom_prenom','pere_profession','mere_nom_prenom','mere_profession','type_acte','date_declaration','declarant','profession_declarant','tribunal','officier_etat_civil','observations'],
            'mariage'=>['epoux','epouse','date_acte','lieu_acte','celebration','notaire1','notaire2','numero_acte','observations'],
            'deces'=>['nom','prenom','date_acte','lieu_acte','declarant','profession_declarant','observations'],
            default=>['nom_concerne','precision_demande']
        };
        $d=[]; foreach($fields as $k) $d[$k]=trim((string)($p[$k]??'')); $errors=[];
        $required=match($type){
            'naissance'=>['nom','prenom','date_naissance','lieu_naissance','sexe','pere_nom_prenom','pere_profession','mere_nom_prenom','mere_profession','type_acte','date_declaration'],
            'mariage'=>['epoux','epouse','date_acte','lieu_acte','celebration'],
            'deces'=>['nom','prenom','date_acte','lieu_acte'],
            default=>['nom_concerne','precision_demande']
        };
        foreach($required as $k) if($d[$k]==='') $errors[]='Complétez le champ obligatoire : '.str_replace('_',' ',$k).'.';
        if($type==='naissance' && !in_array($d['sexe'],['Féminin','Masculin'],true)) $errors[]='Choisissez le sexe dans la liste.';
        if($type==='naissance' && !in_array($d['type_acte'],['original','declaration','jugement'],true)) $errors[]='Choisissez l’origine de l’acte.';
        if($type==='naissance' && $d['type_acte']==='jugement' && $d['tribunal']==='') $errors[]='Précisez le tribunal.';
        if($type==='naissance' && $d['type_acte']!=='jugement' && ($d['declarant']==='' || $d['profession_declarant']==='')) $errors[]='Précisez le déclarant et sa profession.';
        if($type==='mariage' && !in_array($d['celebration'],['municipalite','notaires','municipalite_et_notaires'],true)) $errors[]='Choisissez le ou les modes de célébration.';
        if($type==='mariage' && in_array($d['celebration'],['notaires','municipalite_et_notaires'],true) && ($d['notaire1']==='' || $d['notaire2']==='')) $errors[]='Indiquez le nom des deux notaires.';
        foreach(['date_naissance','date_declaration','date_acte'] as $k) if($d[$k]??'') { $dt=\DateTimeImmutable::createFromFormat('!Y-m-d',$d[$k]); $err=\DateTimeImmutable::getLastErrors(); if(!$dt || ($err && ($err['warning_count']||$err['error_count'])) || $dt>new \DateTimeImmutable('today')) $errors[]='Vérifiez la date saisie.'; }
        foreach($d as $k=>$v) if(mb_strlen($v,'UTF-8')>1000) $errors[]='Un champ dépasse la longueur autorisée.';
        $name=match($type){'naissance','deces'=>trim(($d['prenom']??'').' '.($d['nom']??'')),'mariage'=>trim(($d['epoux']??'').' / '.($d['epouse']??'')),default=>$d['nom_concerne']??''};
        return [$d,$errors,$name];
    }
    public function services(): void {
        $services=$this->db()->query('SELECT * FROM municipalite_service WHERE actif=1 ORDER BY id_service')->fetchAll();
        $currentPage='municipalite-services'; $title='Services municipaux · E-Tounsi';
        $this->renderFront('municipalite/services',compact('services','currentPage','title'));
    }
    public function nouveau(): void {
        $id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT)?:0;
        $st=$this->db()->prepare('SELECT * FROM municipalite_service WHERE id_service=? AND actif=1'); $st->execute([$id]); $service=$st->fetch();
        if(!$service){$this->flash('danger','Service municipal introuvable.');$this->redirect('municipalite-services');}
        $currentPage='municipalite-services'; $title=$service['libelle'].' · E-Tounsi';
        $delegations=require __DIR__.'/../Data/municipalite-delegations.php';
        $this->renderFront('municipalite/formulaire',compact('service','delegations','currentPage','title'));
    }
    public function enregistrer(): void {
        $id=(int)($_POST['id_service']??0); $st=$this->db()->prepare('SELECT * FROM municipalite_service WHERE id_service=? AND actif=1');$st->execute([$id]);$s=$st->fetch();
        if(!$s){$this->flash('danger','Service municipal introuvable.');$this->redirect('municipalite-services');}
        $tel=$this->phone((string)($_POST['telephone_contact']??''));$errs=[]; if(!$tel)$errs[]='Téléphone tunisien invalide : utilisez 8 chiffres, par exemple 20123456.';
        $type=$s['type_formulaire'];[$d,$more,$name]=$this->details($type,$_POST);$errs=array_merge($errs,$more);
        $gov=$deleg='';$date=null;
        if($s['mode_traitement']==='rendez_vous'){
            $map=require __DIR__.'/../Data/municipalite-delegations.php';$gov=trim((string)($_POST['gouvernorat']??''));$deleg=trim((string)($_POST['delegation']??''));$raw=trim((string)($_POST['date_souhaitee']??''));$date=\DateTimeImmutable::createFromFormat('Y-m-d\\TH:i',$raw);$err=\DateTimeImmutable::getLastErrors();
            if(!isset($map[$gov])||!in_array($deleg,$map[$gov]??[],true))$errs[]='Choisissez une délégation du gouvernorat sélectionné.';
            if(!$date||($err&&($err['warning_count']||$err['error_count']))||$date<=new \DateTimeImmutable())$errs[]='Choisissez une date de rendez-vous future.';
            $name=trim((string)($_SESSION['prenom']??'').' '.(string)($_SESSION['nom']??''));
        }
        if($errs){$this->flash('danger',implode(' ',$errs));$this->redirect('municipalite-demande',['id'=>$id]);}
        $phone=$tel;$d['telephone_contact']=$phone;
        $birthDate=$d['date_naissance']??$d['date_acte']??null;$birthPlace=$d['lieu_naissance']??$d['lieu_acte']??null;
        $request=new DemandeMunicipale($id,$this->citoyenId(),$phone,$name?:null,$birthDate?:null,$birthPlace?:null,json_encode($d,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$gov?:null,$deleg?:null,$date?$date->format('Y-m-d H:i:s'):null);
        $db=$this->db();$db->beginTransaction();try{
            $q=$db->prepare('INSERT INTO municipalite_demande (id_service,id_citoyen,nom_concerne,telephone_contact,date_acte,lieu_acte,precision_demande,gouvernorat,delegation,date_souhaitee,statut_rdv) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
            $q->execute([$request->getServiceId(),$request->getCitoyenId(),$request->getNomConcerne(),$request->getTelephone(),$request->getDateActe(),$request->getLieuActe(),$request->getDetailsJson(),$request->getGouvernorat(),$request->getDelegation(),$request->getDateSouhaitee(),$request->getDateSouhaitee()?'en_attente':null]);$requestId=(int)$db->lastInsertId();
            if((float)$s['frais']>0){$p=$db->prepare('INSERT INTO municipalite_paiement (id_demande,montant) VALUES (?,?)');$p->execute([$requestId,$s['frais']]);}
            $n=$db->prepare('INSERT INTO municipalite_notification (id_citoyen,id_demande,titre,message) VALUES (?,?,?,?)');$n->execute([$this->citoyenId(),$requestId,'Demande reçue',$date?'Votre demande de rendez-vous est reçue et attend la confirmation de la municipalité.':'Votre demande a été enregistrée. Vous recevrez une notification lorsque son état changera.']);$db->commit();
        }catch(Throwable $e){if($db->inTransaction())$db->rollBack();error_log($e->getMessage());$this->flash('danger','La demande n’a pas pu être enregistrée. Vérifiez que la base de données a été installée.');$this->redirect('municipalite-services');}
        $this->flash('success','Votre demande a été envoyée. Vous pouvez imprimer son récépissé.');$this->redirect('municipalite-recu',['id'=>$requestId]);
    }
    public function suivi(): void {
        $q=$this->db()->prepare('SELECT d.*,s.libelle,s.frais,s.mode_traitement,p.statut AS paiement_statut,p.id_paiement,p.reference AS paiement_reference FROM municipalite_demande d JOIN municipalite_service s ON s.id_service=d.id_service LEFT JOIN municipalite_paiement p ON p.id_demande=d.id_demande WHERE d.id_citoyen=? ORDER BY d.date_depot DESC,d.id_demande DESC');$q->execute([$this->citoyenId()]);$demandes=$q->fetchAll();
        $n=$this->db()->prepare('SELECT * FROM municipalite_notification WHERE id_citoyen=? ORDER BY cree_le DESC LIMIT 30');$n->execute([$this->citoyenId()]);$notifications=$n->fetchAll();$currentPage='municipalite-suivi';$title='Mes demandes municipales · E-Tounsi';
        $cfg=is_file(__DIR__.'/../../config.local.php')?require __DIR__.'/../../config.local.php':[];$carteActive=!empty($cfg['konnect_api_key'])&&!empty($cfg['konnect_wallet_id']);$demoActive=($cfg['app_env']??'production')==='development';
        $this->renderFront('municipalite/suivi',compact('demandes','notifications','currentPage','title','carteActive','demoActive'));
    }
    public function recu(): void {
        $id=(int)($_GET['id']??0);$q=$this->db()->prepare('SELECT d.*,s.libelle,s.frais,s.mode_traitement,p.statut AS paiement_statut,p.id_paiement,p.reference AS paiement_reference FROM municipalite_demande d JOIN municipalite_service s ON s.id_service=d.id_service LEFT JOIN municipalite_paiement p ON p.id_demande=d.id_demande WHERE d.id_demande=? AND d.id_citoyen=?');$q->execute([$id,$this->citoyenId()]);$demande=$q->fetch();if(!$demande){$this->flash('danger','Récépissé introuvable.');$this->redirect('municipalite-suivi');}
        $details=json_decode((string)$demande['precision_demande'],true)?:[];$currentPage='municipalite-suivi';$title='Récépissé DM-'.str_pad((string)$id,5,'0',STR_PAD_LEFT).' · E-Tounsi';$cfg=is_file(__DIR__.'/../../config.local.php')?require __DIR__.'/../../config.local.php':[];$carteActive=!empty($cfg['konnect_api_key'])&&!empty($cfg['konnect_wallet_id']);$demoActive=($cfg['app_env']??'production')==='development';$this->renderFront('municipalite/recu',compact('demande','details','currentPage','title','carteActive','demoActive'));
    }
    public function payer(): void {
        $id=(int)($_POST['id_demande']??0);$q=$this->db()->prepare('SELECT p.*,d.id_citoyen FROM municipalite_paiement p JOIN municipalite_demande d ON d.id_demande=p.id_demande WHERE p.id_demande=? AND d.id_citoyen=? AND p.statut="en_attente"');$q->execute([$id,$this->citoyenId()]);$p=$q->fetch();
        if(!$p){$this->flash('warning','Aucun paiement en attente pour cette demande.');$this->redirect('municipalite-suivi');}
        $cfg=is_file(__DIR__.'/../../config.local.php')?require __DIR__.'/../../config.local.php':[];
        if(!empty($cfg['konnect_api_key'])&&!empty($cfg['konnect_wallet_id'])){
            try{$root=rtrim((string)($cfg['public_url']??''),'/').'/index.php';if($root==='/index.php')throw new RuntimeException('Définissez public_url dans config.local.php.');$payload=['receiverWalletId'=>$cfg['konnect_wallet_id'],'token'=>'TND','amount'=>(int)round((float)$p['montant']*1000),'type'=>'immediate','description'=>'Frais de demande municipale #'.$id,'acceptedPaymentMethods'=>['bank_card'],'lifespan'=>20,'checkoutForm'=>true,'addPaymentFeesToAmount'=>false,'orderId'=>'ETM-'.$id.'-'.$p['id_paiement'],'successUrl'=>$root.'?page=municipalite-paiement-retour&id='.$id,'failUrl'=>$root.'?page=municipalite-paiement-retour&id='.$id.'&cancel=1'];$ctx=stream_context_create(['http'=>['method'=>'POST','header'=>"Accept: application/json\r\nContent-Type: application/json\r\nx-api-key: ".$cfg['konnect_api_key']."\r\n",'content'=>json_encode($payload),'timeout'=>20,'ignore_errors'=>true]]);$raw=file_get_contents(rtrim($cfg['konnect_api_base']??'https://api.sandbox.konnect.network/api/v2','/').'/payments/init-payment',false,$ctx);$j=json_decode((string)$raw,true);$pay=$j['payment']??$j;if(empty($pay['payUrl'])||empty($pay['paymentRef'])||!str_ends_with(strtolower((string)parse_url($pay['payUrl'],PHP_URL_HOST)),'konnect.network'))throw new RuntimeException('Lien hébergé Konnect absent ou invalide.');$u=$this->db()->prepare('UPDATE municipalite_paiement SET mode_paiement="konnect",reference=? WHERE id_paiement=?');$u->execute([$pay['paymentRef'],$p['id_paiement']]);header('Location: '.$pay['payUrl']);exit;}catch(Throwable $e){$this->flash('danger','Paiement Konnect non initialisé. Vérifiez sa configuration puis réessayez.');error_log('Konnect municipalite: '.$e->getMessage());$this->redirect('municipalite-suivi');}
        }
        $this->flash('info','Paiement de démonstration uniquement : ajoutez vos identifiants Konnect dans config.local.php pour activer le règlement par carte.');$this->redirect('municipalite-suivi');
    }
    public function retourPaiement(): void {
        $id=(int)($_GET['id']??0);$q=$this->db()->prepare('SELECT p.*,d.id_citoyen FROM municipalite_paiement p JOIN municipalite_demande d ON d.id_demande=p.id_demande WHERE p.id_demande=? AND d.id_citoyen=?');$q->execute([$id,$this->citoyenId()]);$p=$q->fetch();if(!$p){$this->redirect('municipalite-suivi');}
        if(isset($_GET['cancel'])){$this->flash('warning','Paiement interrompu : aucun règlement n’a été confirmé.');$this->redirect('municipalite-suivi');}
        $cfg=is_file(__DIR__.'/../../config.local.php')?require __DIR__.'/../../config.local.php':[];try{$ctx=stream_context_create(['http'=>['method'=>'GET','header'=>"Accept: application/json\r\nx-api-key: ".($cfg['konnect_api_key']??'')."\r\n",'timeout'=>20,'ignore_errors'=>true]]);$raw=file_get_contents(rtrim($cfg['konnect_api_base']??'https://api.sandbox.konnect.network/api/v2','/').'/payments/'.rawurlencode((string)$p['reference']),false,$ctx);$json=json_decode((string)$raw,true);$pay=$json['payment']??[];$expected='ETM-'.$id.'-'.$p['id_paiement'];$success=false;foreach(($pay['transactions']??[]) as $tx)if(($tx['status']??'')==='success')$success=true;$ok=($pay['status']??'')==='completed'&&($pay['token']??'')==='TND'&&(int)($pay['amount']??-1)===(int)round((float)$p['montant']*1000)&&(int)($pay['reachedAmount']??-1)>=(int)round((float)$p['montant']*1000)&&($pay['orderId']??'')===$expected&&$success;if($ok){$this->db()->prepare('UPDATE municipalite_paiement SET statut="payee",paye_le=NOW() WHERE id_paiement=? AND statut="en_attente"')->execute([$p['id_paiement']]);$this->flash('success','Paiement par carte confirmé.');}else $this->flash('warning','Le paiement n’est pas encore confirmé par Konnect.');}catch(Throwable $e){$this->flash('danger','La confirmation de paiement est momentanément indisponible.');}
        $this->redirect('municipalite-recu',['id'=>$id]);
    }
    public function payerDemo(): void {
        $cfg=is_file(__DIR__.'/../../config.local.php')?require __DIR__.'/../../config.local.php':[];
        if(($cfg['app_env']??'production')!=='development'){$this->flash('danger','Le paiement de démonstration est désactivé hors du mode développement.');$this->redirect('municipalite-suivi');}
        $id=(int)($_POST['id_demande']??0);$q=$this->db()->prepare('SELECT p.id_paiement FROM municipalite_paiement p JOIN municipalite_demande d ON d.id_demande=p.id_demande WHERE p.id_demande=? AND d.id_citoyen=? AND p.statut="en_attente"');$q->execute([$id,$this->citoyenId()]);$p=$q->fetch();if(!$p){$this->flash('warning','Aucun paiement en attente.');$this->redirect('municipalite-suivi');}
        $ref='DEMO-ETM-'.$id.'-'.bin2hex(random_bytes(4));$this->db()->prepare('UPDATE municipalite_paiement SET statut="payee",mode_paiement="demo",reference=?,paye_le=NOW() WHERE id_paiement=?')->execute([$ref,$p['id_paiement']]);$this->db()->prepare('INSERT INTO municipalite_notification (id_citoyen,id_demande,titre,message) VALUES (?,?,?,?)')->execute([$this->citoyenId(),$id,'Paiement de démonstration','Le paiement de démonstration a été enregistré. Aucune transaction bancaire réelle n’a eu lieu.']);$this->flash('info','Paiement de démonstration enregistré. Aucune somme n’a été débitée.');$this->redirect('municipalite-recu',['id'=>$id]);
    }
    public function demandesAdmin(): void {
        $this->requireMunicipalitePermission('consulter_demandes');$demandes=$this->db()->query('SELECT d.*,s.libelle,c.nom AS citoyen_nom,c.prenom,c.email,p.statut AS paiement_statut FROM municipalite_demande d JOIN municipalite_service s ON s.id_service=d.id_service JOIN citoyen c ON c.id_citoyen=d.id_citoyen LEFT JOIN municipalite_paiement p ON p.id_demande=d.id_demande ORDER BY d.date_depot DESC,d.id_demande DESC')->fetchAll();$currentPage='admin-demandes-municipales';$title='Demandes municipales · Back Office';$peutTraiter=($_SESSION['role']??'')==='admin'||self::hasMunicipalitePermission($this->citoyenId(),'traiter_demandes');$this->renderBack('municipalite/demandes',compact('demandes','currentPage','title','peutTraiter'));
    }
    public function statutAdmin(): void {
        $this->requireMunicipalitePermission('traiter_demandes');$id=(int)($_POST['id_demande']??0);$status=(string)($_POST['statut']??'');$allowed=['en_attente','en_cours','approuvee','document_pret','rejetee'];if(!in_array($status,$allowed,true)){$this->flash('danger','Statut invalide.');$this->redirect('admin-demandes-municipales');}
        $db=$this->db();$q=$db->prepare('SELECT id_citoyen,statut FROM municipalite_demande WHERE id_demande=?');$q->execute([$id]);$before=$q->fetch();if(!$before){$this->flash('danger','Demande introuvable.');$this->redirect('admin-demandes-municipales');}$db->prepare('UPDATE municipalite_demande SET statut=?,statut_rdv=CASE WHEN date_souhaitee IS NULL THEN statut_rdv WHEN ?="approuvee" THEN "confirme" WHEN ?="rejetee" THEN "refuse" ELSE statut_rdv END WHERE id_demande=?')->execute([$status,$status,$status,$id]);
        if($before['statut']!==$status){$text=['en_cours'=>'Votre demande est en cours de traitement.','approuvee'=>'Votre demande a été approuvée.','document_pret'=>'Votre document est prêt à retirer au guichet municipal.','rejetee'=>'Votre demande a été refusée. Contactez la municipalité pour les précisions.'][$status]??'Le statut de votre demande a été mis à jour.';$db->prepare('INSERT INTO municipalite_notification (id_citoyen,id_demande,titre,message) VALUES (?,?,?,?)')->execute([$before['id_citoyen'],$id,'Mise à jour municipale',$text]);}
        $this->flash('success','Statut de la demande mis à jour.');$this->redirect('admin-demandes-municipales');
    }
    public function servicesAdmin(): void {
        CitoyenController::requireAdmin();$services=$this->db()->query('SELECT * FROM municipalite_service ORDER BY id_service')->fetchAll();$currentPage='admin-services-municipaux';$title='Services municipaux · Back Office';$this->renderBack('municipalite/services-admin',compact('services','currentPage','title'));
    }
    public function agentsMunicipalite(): void {
        CitoyenController::requireAdmin();
        $agents=$this->db()->query('SELECT c.id_citoyen,c.nom,c.prenom,c.email,c.actif,a.consulter_demandes,a.traiter_demandes FROM citoyen c LEFT JOIN municipalite_agent_access a ON a.id_citoyen=c.id_citoyen WHERE c.role="agent" ORDER BY c.nom,c.prenom,c.id_citoyen')->fetchAll();
        $currentPage='admin-municipalite-agents';$title='Autorisations des agents municipaux · E-Tounsi';
        $this->renderBack('municipalite/agents',compact('agents','currentPage','title'));
    }
    public function affecterAgentMunicipalite(): void {
        CitoyenController::requireAdmin();$id=(int)($_POST['id_citoyen']??0);$consulter=isset($_POST['consulter_demandes'])&&$_POST['consulter_demandes']==='1';$traiter=isset($_POST['traiter_demandes'])&&$_POST['traiter_demandes']==='1';
        $st=$this->db()->prepare('SELECT id_citoyen FROM citoyen WHERE id_citoyen=? AND role="agent"');$st->execute([$id]);
        if(!$st->fetchColumn()){$this->flash('danger','Seul un compte ayant le rôle Agent peut recevoir ces autorisations.');$this->redirect('admin-municipalite-agents');}
        if($traiter)$consulter=true;
        if(!$consulter&&!$traiter){$this->db()->prepare('DELETE FROM municipalite_agent_access WHERE id_citoyen=?')->execute([$id]);}
        else{$this->db()->prepare('INSERT INTO municipalite_agent_access (id_citoyen,consulter_demandes,traiter_demandes,modifie_par) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE consulter_demandes=VALUES(consulter_demandes),traiter_demandes=VALUES(traiter_demandes),modifie_par=VALUES(modifie_par),modifie_le=CURRENT_TIMESTAMP')->execute([$id,(int)$consulter,(int)$traiter,$this->citoyenId()]);}
        auditEvenement('MUNICIPALITE_ACCESS_CHANGED','Autorisations Municipalité modifiées pour le compte agent #'.$id.'.',$id);
        $this->flash('success','Autorisations de l’agent municipal mises à jour.');$this->redirect('admin-municipalite-agents');
    }
}
