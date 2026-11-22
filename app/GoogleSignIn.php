<?php
declare(strict_types=1);
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) require_once $autoload;

final class GoogleSignIn {
    private Google\Auth\AccessToken $verifier;
    private GuzzleHttp\ClientInterface $http;
    public function __construct(private array $config, ?Google\Auth\AccessToken $verifier = null, ?GuzzleHttp\ClientInterface $http = null) {
        $this->http = $http ?? new GuzzleHttp\Client(['timeout'=>10,'connect_timeout'=>5]);
        $this->verifier = $verifier ?? new Google\Auth\AccessToken(Google\Auth\HttpHandler\HttpHandlerFactory::build($this->http));
    }
    public static function configuration(): array {
        $file = dirname(__DIR__) . '/google.local.php';
        return is_file($file) ? require $file : [];
    }
    public static function configured(): bool {
        $c = self::configuration();
        $uri = $c['redirect_uri']??''; $parts = is_string($uri) ? parse_url($uri) : false;
        return class_exists(Google\Auth\AccessToken::class)
            && is_string($c['client_id']??null) && str_ends_with($c['client_id'],'.apps.googleusercontent.com')
            && is_string($c['client_secret']??null) && $c['client_secret']!=='' && $parts
            && (($parts['scheme']??'')==='https' || (($parts['scheme']??'')==='http' && in_array($parts['host']??'',['localhost','127.0.0.1'],true)));
    }
    public function begin(array &$session, ?array $link = null): string {
        $state = bin2hex(random_bytes(32)); $nonce = bin2hex(random_bytes(32));
        $verifier = rtrim(strtr(base64_encode(random_bytes(48)),'+/','-_'),'=');
        $session['google_oauth'] = ['state'=>$state,'nonce'=>$nonce,'verifier'=>$verifier,'expires'=>time()+600,'link'=>$link];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'=>$this->config['client_id'],'redirect_uri'=>$this->config['redirect_uri'],
            'response_type'=>'code','scope'=>'openid email profile','state'=>$state,'nonce'=>$nonce,
            'code_challenge'=>rtrim(strtr(base64_encode(hash('sha256',$verifier,true)),'+/','-_'),'='),
            'code_challenge_method'=>'S256','prompt'=>'select_account','max_age'=>'0',
        ],'', '&',PHP_QUERY_RFC3986);
    }
    public static function consume(array &$session, mixed $state): array {
        $flow = $session['google_oauth']??null; unset($session['google_oauth']);
        if (!is_array($flow) || !is_string($state) || !hash_equals($flow['state'],$state) || $flow['expires']<time())
            throw new DomainException('La connexion Google a expiré ou ne correspond pas à cette session. Réessayez.',400);
        return $flow;
    }
    public function exchange(string $code, array $flow): array {
        if ($code==='' || strlen($code)>4096) throw new DomainException('Code Google invalide.',400);
        $response = $this->http->request('POST','https://oauth2.googleapis.com/token',['form_params'=>[
            'code'=>$code,'client_id'=>$this->config['client_id'],'client_secret'=>$this->config['client_secret'],
            'redirect_uri'=>$this->config['redirect_uri'],'grant_type'=>'authorization_code','code_verifier'=>$flow['verifier'],
        ]]);
        $tokens = json_decode((string)$response->getBody(),true,512,JSON_THROW_ON_ERROR);
        if (!is_string($tokens['id_token']??null)) throw new DomainException('Google n’a pas fourni d’identité vérifiable.',400);
        return $this->verify($tokens['id_token'],$flow['nonce']);
    }
    public function verify(string $token, string $nonce): array {
        $claims = $this->verifier->verify($token,['audience'=>$this->config['client_id']]);
        if (!$claims || !in_array($claims['iss']??'',['https://accounts.google.com','accounts.google.com'],true)
            || ($claims['aud']??null)!==$this->config['client_id'] || ($claims['azp']??$this->config['client_id'])!==$this->config['client_id']
            || !is_string($claims['nonce']??null) || !hash_equals($nonce,$claims['nonce'])
            || !is_string($claims['sub']??null) || !preg_match('/^[a-zA-Z0-9_-]{1,255}$/D',$claims['sub'])
            || ($claims['email_verified']??false)!==true || !filter_var($claims['email']??null,FILTER_VALIDATE_EMAIL)
            || ($claims['exp']??0)<=time() || ($claims['iat']??0)>time()+30)
            throw new DomainException('L’identité Google n’a pas pu être vérifiée.',400);
        return $claims;
    }
}
