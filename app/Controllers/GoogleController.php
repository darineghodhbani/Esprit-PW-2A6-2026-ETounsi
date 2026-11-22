<?php
declare(strict_types=1);

require_once __DIR__ . '/CnamController.php';
require_once __DIR__ . '/../Models/GoogleIdentity.php';
require_once __DIR__ . '/../GoogleSignIn.php';
require_once __DIR__ . '/CitoyenController.php';
require_once __DIR__ . '/AuditController.php';

final class GoogleController extends CnamController {
    public function liee(int $citoyen): bool { return $this->one('SELECT google_sub FROM google_identities WHERE id_citoyen=?',[$citoyen])!==null; }
    /** Receives only claims already verified by GoogleSignIn, never the browser's claims. */
    public function connecter(array $profile, ?array $liaison = null): array {
        try {
            return $this->transaction(function () use ($profile,$liaison) {
                $identity = $this->one('SELECT * FROM google_identities WHERE google_sub=? FOR UPDATE',[$profile['sub']]);
                if ($liaison!==null) {
                    $user = $this->one("SELECT id_citoyen,email,version_session FROM citoyen WHERE id_citoyen=? AND actif=1 AND role='citoyen' FOR UPDATE",[$liaison['id']]);
                    if (!$user || (int)$user['version_session']!==$liaison['version']) throw new DomainException('Votre compte a changé. Reconnectez-vous.',403);
                    if (strtolower($user['email'])!==strtolower($profile['email'])) throw new DomainException('Le compte Google doit utiliser la même adresse que votre compte E-Tounsi.',409);
                    if (($identity && (int)$identity['id_citoyen']!==(int)$user['id_citoyen']) || $this->liee((int)$user['id_citoyen']))
                        throw new DomainException('Ce compte possède déjà une liaison Google.',409);
                    $id = (int)$user['id_citoyen'];
                } elseif ($identity) {
                    $id = (int)$identity['id_citoyen'];
                } else {
                    if ($this->one('SELECT id_citoyen FROM citoyen WHERE email=? FOR UPDATE',[$profile['email']]))
                        throw new DomainException('Cette adresse possède déjà un compte. Connectez-vous avec votre mot de passe puis liez Google depuis votre profil.',409);
                    $nom = mb_substr(trim((string)($profile['family_name']??$profile['name']??'Compte Google')),0,80);
                    $prenom = mb_substr(trim((string)($profile['given_name']??'')),0,80);
                    $this->execute("INSERT INTO citoyen(nom,prenom,email,telephone,mot_de_passe,role,actif) VALUES (?,?,?,'',?,'citoyen',1)",
                        [$nom,$prenom,strtolower($profile['email']),password_hash(bin2hex(random_bytes(32)),PASSWORD_DEFAULT)]);
                    $id = (int)$this->db->lastInsertId();
                    (new AuditController($this->db))->log($id, 'USER_CREATED', 'Compte citoyen créé avec Google.', $id);
                }
                if (!$identity) $this->execute('INSERT INTO google_identities(google_sub,id_citoyen) VALUES (?,?)',[$profile['sub'],$id]);
                $user = (new CitoyenController())->findById($id);
                if (!$user || $user['actif']!==1 || $user['role']!=='citoyen') throw new DomainException('La connexion Google est réservée à un compte citoyen actif.',403);
                (new AuditController($this->db))->log($id, 'LOGIN', 'Connexion citoyenne avec Google.', $id);
                return $user;
            });
        } catch (PDOException $e) {
            if (($e->errorInfo[1]??0)===1062) throw new DomainException('Cette identité ou cette adresse est déjà associée. Reconnectez-vous.',409,$e);
            throw $e;
        }
    }

    public function start(): void {
        if (!GoogleSignIn::configured()) { $this->problem('Le client OAuth Google n’est pas configuré pour cet environnement. Renseignez google.local.php en suivant le guide d’installation.',503); return; }
        try {
            $link = null;
            if (($_POST['mode']??'login')==='link') {
                $user = exigerRole(['citoyen']);
                $password = is_string($_POST['password']??null) ? $_POST['password'] : '';
                $verified = (new CitoyenController())->verifyLogin($user['email'], $password);
                if (!$verified) throw new DomainException('Confirmez votre mot de passe actuel pour lier Google.',403);
                $link = ['id'=>$user['id'],'version'=>$user['version_session']];
            }
            $provider = new GoogleSignIn(GoogleSignIn::configuration());
            header('Location: ' . $provider->begin($_SESSION,$link),true,303); exit;
        } catch (DomainException $e) { $this->problem($e->getMessage(),$e->getCode()?:400); }
    }
    public function callback(): void {
        $phase = 'session_state';
        try {
            $flow = GoogleSignIn::consume($_SESSION,$_GET['state']??null);
            if (!GoogleSignIn::configured()) throw new DomainException('La connexion Google est temporairement indisponible.',503);
            if (isset($_GET['error'])) throw new DomainException('La connexion Google a été annulée.',400);
            if ($flow['link']!==null && (int)($_SESSION['user_id']??0)!==$flow['link']['id']) throw new DomainException('La session de liaison a changé.',403);
            $code = is_string($_GET['code']??null) ? $_GET['code'] : '';
            $phase = 'token_exchange_and_verification';
            $claims = (new GoogleSignIn(GoogleSignIn::configuration()))->exchange($code,$flow);
            $phase = 'citizen_identity';
            $user = $this->connecter($claims,$flow['link']);
            $phase = 'session_creation';
            connecterUtilisateur($user);
            $_SESSION['flash'] = ['type'=>'success','message'=>$flow['link'] ? 'Votre compte Google est maintenant lié.' : 'Connexion Google réussie.'];
            header('Location: index.php?page=' . ($flow['link']?'profile':'home'),true,303); exit;
        } catch (DomainException $e) { $this->problem($e->getMessage(),$e->getCode()?:400); }
        catch (Throwable $e) {
            $diagnostic = ['phase'=>$phase,'type'=>get_class($e),'code'=>$e->getCode()];
            if ($e instanceof PDOException) {
                $diagnostic['sqlstate'] = $e->errorInfo[0] ?? null;
                $diagnostic['driver_code'] = $e->errorInfo[1] ?? null;
            }
            if ($e instanceof GuzzleHttp\Exception\RequestException) {
                $response = $e->getResponse();
                $diagnostic['http_status'] = $response?->getStatusCode();
                $providerError = $response ? json_decode((string)$response->getBody(), true) : null;
                $diagnostic['provider_error'] = is_array($providerError) ? ($providerError['error'] ?? null) : null;
            }
            error_log('Connexion Google indisponible : ' . json_encode($diagnostic, JSON_UNESCAPED_SLASHES));
            $this->problem('La connexion Google est temporairement indisponible. Vous pouvez utiliser votre mot de passe.',503);
        }
    }
    private function problem(string $message, int $status): void {
        http_response_code($status);
        $this->renderFront('googleError',['title'=>tr('Connexion Google','الدخول بحساب Google'),'currentPage'=>'login','message'=>$message]);
    }
}
