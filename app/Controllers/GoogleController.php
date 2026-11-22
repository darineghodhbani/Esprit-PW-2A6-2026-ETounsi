<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../GoogleSignIn.php';
require_once __DIR__ . '/../Models/GoogleIdentity.php';

final class GoogleController extends Controller {
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
            $user = (new GoogleIdentity())->connecter($claims,$flow['link']);
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
