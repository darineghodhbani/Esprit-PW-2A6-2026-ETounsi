<?php
declare(strict_types=1);

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/DocumentCNAM.php';

final class DocumentCNAMController extends CnamController {
    private const MIMES = ['pdf'=>'application/pdf','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg'];
    public static function directory(): string {
        $project = dirname(__DIR__,2);
        $dir = getenv('CNAM_STORAGE_DIR') ?: dirname($project) . '/' . basename($project) . '-private/cnam';
        if (!is_dir($dir) && !mkdir($dir,0700,true) && !is_dir($dir)) throw new RuntimeException('Stockage CNAM indisponible.');
        $dir = realpath($dir);
        if (!$dir || str_starts_with($dir . '/',realpath($project) . '/')) throw new RuntimeException('Le stockage CNAM doit être hors de la racine web.');
        chmod($dir,0700);
        return $dir;
    }
    public static function normaliser(array $upload): array {
        if (!isset($upload['error'])) return [];
        if (!is_array($upload['error'])) return $upload['error'] === UPLOAD_ERR_NO_FILE ? [] : [$upload];
        $files = [];
        foreach ($upload['error'] as $i=>$error) {
            if ($error === UPLOAD_ERR_NO_FILE) continue;
            $file = [];
            foreach (['name','tmp_name','error','size'] as $key) $file[$key] = $upload[$key][$i]??null;
            $files[] = $file;
        }
        return $files;
    }
    public function stocker(array $files, string $type): array {
        if (count($files) > 5) throw new DomainException('Ajoutez cinq justificatifs au maximum par envoi.',422);
        $valid = []; $total = 0;
        foreach ($files as $f) {
            if (($f['error']??null) !== UPLOAD_ERR_OK || !is_string($f['tmp_name']??null) || !is_uploaded_file($f['tmp_name']))
                throw new DomainException('Un justificatif n’a pas été transmis correctement.',422);
            $size = filesize($f['tmp_name']); $total += $size;
            $ext = is_string($f['name']??null) ? strtolower(pathinfo($f['name'],PATHINFO_EXTENSION)) : '';
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            if ($size < 1 || $size > 5*1024*1024 || $total > 15*1024*1024 || (self::MIMES[$ext]??null) !== $mime)
                throw new DomainException('Justificatif refusé : PDF, PNG ou JPEG, 5 Mo par fichier et 15 Mo par envoi.',422);
            if ($ext !== 'pdf') {
                $dimensions = @getimagesize($f['tmp_name']);
                if (!$dimensions || $dimensions[0]*$dimensions[1] > 40000000) throw new DomainException('Image illisible ou trop grande.',422);
            } else {
                $fp = fopen($f['tmp_name'],'rb'); $head = fread($fp,8); fseek($fp,max(0,$size-1024)); $tail = stream_get_contents($fp); fclose($fp);
                if (!str_starts_with($head,'%PDF-') || !str_contains($tail,'%%EOF')) throw new DomainException('Le document PDF est incomplet.',422);
            }
            $name = mb_substr(preg_replace('/[\x00-\x1f\x7f]/u','',basename($f['name']))??'justificatif',0,180);
            $valid[] = ['tmp'=>$f['tmp_name'],'fichier'=>bin2hex(random_bytes(16)) . '.' . ($ext==='jpeg'?'jpg':$ext),
                'nom_original'=>$name,'mime'=>$mime,'taille'=>$size,'type_document'=>$type];
        }
        $stored = [];
        try {
            foreach ($valid as $file) {
                $path = self::directory() . '/' . $file['fichier'];
                if (!move_uploaded_file($file['tmp'],$path)) throw new RuntimeException('Échec du stockage privé.');
                chmod($path,0600); unset($file['tmp']); $stored[] = $file;
            }
        } catch (Throwable $e) { self::effacerFichiers($stored); throw $e; }
        return $stored;
    }
    public function attacher(int $beneficiaire, ?int $demande, array $files): void {
        foreach ($files as $f) $this->execute('INSERT INTO document_cnam(id_beneficiaire,id_demande,type_document,fichier,nom_original,mime,taille) VALUES (?,?,?,?,?,?,?)',
            [$beneficiaire,$demande,$f['type_document'],$f['fichier'],$f['nom_original'],$f['mime'],$f['taille']]);
    }
    public static function effacerFichiers(array $files): void {
        foreach ($files as $f) if (preg_match('/^[a-f0-9]{32}\.(pdf|png|jpg)$/D',$f['fichier']??'')) {
            $path = self::directory() . '/' . $f['fichier']; if (is_file($path)) unlink($path);
        }
    }
    public function beneficiaire(int $id): array { return $this->all('SELECT * FROM document_cnam WHERE id_beneficiaire=? AND id_demande IS NULL ORDER BY id_document',[$id]); }
    public function demande(int $id): array { return $this->all('SELECT * FROM document_cnam WHERE id_demande=? ORDER BY id_document',[$id]); }
    public function pourDemandes(array $ids): array { return $this->grouper('id_demande',$ids,false); }
    public function pourBeneficiaires(array $ids): array { return $this->grouper('id_beneficiaire',$ids,true); }
    private function grouper(string $colonne, array $ids, bool $sansDemande): array {
        if (!in_array($colonne,['id_demande','id_beneficiaire'],true)) throw new InvalidArgumentException('Colonne de regroupement invalide.');
        $ids=array_values(array_unique(array_filter(array_map('intval',$ids),static fn($id)=>$id>0)));
        if (!$ids) return [];
        $placeholders=implode(',',array_fill(0,count($ids),'?'));
        $where='WHERE '.$colonne.' IN ('.$placeholders.')'.($sansDemande?' AND id_demande IS NULL':'');
        $rows=$this->all('SELECT * FROM document_cnam '.$where.' ORDER BY id_document',$ids); $groupes=[];
        foreach ($rows as $row) $groupes[(int)$row[$colonne]][]=$row;
        return $groupes;
    }
    public function avecProprietaire(int $id): ?array {
        return $this->one('SELECT d.*,a.id_citoyen FROM document_cnam d JOIN beneficiaire b ON b.id_beneficiaire=d.id_beneficiaire JOIN assure_social a ON a.id_assure=b.id_assure WHERE d.id_document=?',[$id]);
    }

    public function telecharger(): void {
        $user=exigerRole(['citoyen','agent','admin']);
        try { $id=CnamValidation::id($_GET['id']??null); }
        catch (DomainException $e) { http_response_code(400); exit('Identifiant invalide.'); }
        $doc=$this->avecProprietaire($id);
        if (!$doc) { http_response_code(404); exit('Document introuvable.'); }
        if ($user['role']==='citoyen' && (int)$doc['id_citoyen']!==$user['id']) { http_response_code(404); exit('Document introuvable.'); }
        if ($user['role']!=='citoyen') $this->utilisateurCNAM();
        if (!preg_match('/^[a-f0-9]{32}\.(pdf|png|jpg)$/D',$doc['fichier'])) { http_response_code(404); exit('Document introuvable.'); }
        $path=DocumentCNAMController::directory().'/'.$doc['fichier'];
        if (!is_file($path) || !is_readable($path)) { http_response_code(404); exit('Document introuvable.'); }
        header('Content-Type: '.$doc['mime']); header('Content-Length: '.filesize($path));
        header('Content-Disposition: attachment; filename="justificatif-'.$id.'.'.pathinfo($path,PATHINFO_EXTENSION).'"');
        header("Content-Security-Policy: default-src 'none'; sandbox"); header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store'); readfile($path); exit;
    }
}
