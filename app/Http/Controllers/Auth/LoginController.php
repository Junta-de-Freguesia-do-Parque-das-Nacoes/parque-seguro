<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SamlNonce;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ldap;
use App\Services\Saml;
use Com\Tecnick\Barcode\Barcode;
use Google2FA;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Redirect;

/**
 * This controller handles authentication for the user, including local
 * database users and LDAP users.
 *
 * @author [A. Gianotto] [<snipe@snipe.net>]
 * @version    v1.0
 */
class LoginController extends Controller
{
    use ThrottlesLogins;

    // This tells the auth controller to use username instead of email address
    protected $username = 'username';

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @var Saml
     */
    protected $saml;

    /**
     * Create a new authentication controller instance.
     *
     * @param Saml $saml
     *
     * @return void
     */
    public function __construct(Saml $saml)
    {
        parent::__construct();
        $this->middleware('guest', ['except' => ['logout', 'postTwoFactorAuth', 'getTwoFactorAuth', 'getTwoFactorEnroll']]);
        Session::put('backUrl', \URL::previous());
        $this->saml = $saml;
    }

    public function showLoginForm(Request $request)
    {
        $this->loginViaRemoteUser($request);
        $this->loginViaSaml($request);
        if (Auth::check()) {
            return redirect()->intended('/');
        }

        if (!$request->session()->has('loggedout')) {
            // If the environment is set to ALWAYS require SAML, go straight to the SAML route.
            // We don't need to check other settings, as this should override those.
            if (config('app.require_saml')) {
                return redirect()->route('saml.login');
            }


            if ($this->saml->isEnabled() && Setting::getSettings()->saml_forcelogin == '1' && ! ($request->has('nosaml') || $request->session()->has('error'))) {
                return redirect()->route('saml.login');
            }
        }

        if (Setting::getSettings()->login_common_disabled == '1') {
            return view('errors.403');
        }

        return view('auth.login');
    }

    /**
     * Log in a user by SAML
     *
     * @author Johnson Yi <jyi.dev@outlook.com>
     *
     * @since 5.0.0
     *
     * @param Request $request
     *
     * @return User
     *
     * @throws \Exception
     */
    private function loginViaSaml(Request $request)
    {
        $saml = $this->saml;
        $samlData = $request->session()->get('saml_login');

        if ($saml->isEnabled() && ! empty($samlData)) {

            try {
                $user = $saml->samlLogin($samlData);
                $notValidAfter = new \Carbon\Carbon(@$samlData['assertionNotOnOrAfter']);
                if(\Carbon::now()->greaterThanOrEqualTo($notValidAfter)) {
                    abort(400,"Expired SAML Assertion");
                }
                if(SamlNonce::where('nonce', @$samlData['nonce'])->count() > 0) {
                    abort(400,"Assertion has already been used");
                }
                Log::debug("okay, fine, this is a new nonce then. Good for you.");
                if (!is_null($user)) {
                    Auth::login($user);
                } else {
                    $username = $saml->getUsername();
                    Log::debug("SAML user '$username' could not be found in database.");
                    $request->session()->flash('error', trans('auth/message.signin.error'));
                    $saml->clearData();
                }

                if ($user = auth()->user()) {
                    $user->last_login = \Carbon::now();
                    $user->saveQuietly();
                }
                $s = new SamlNonce();
                $s->nonce = @$samlData['nonce'];
                $s->not_valid_after = $notValidAfter;
                $s->save();

            } catch (\Exception $e) {
                Log::debug('There was an error authenticating the SAML user: '.$e->getMessage());
                throw $e;
            }

        // Fallthrough with better logging
        } else {

            // Better logging
            if (empty($samlData)) {
                Log::debug("SAML page requested, but samlData seems empty.");
            }
        }



    }

    /**
     * Log in a user by LDAP
     *
     * @author Wes Hulette <jwhulette@gmail.com>
     *
     * @since 5.0.0
     *
     * @param Request $request
     *
     * @return User
     *
     * @throws \Exception
     */
    private function loginViaLdap(Request $request)
    {
        $input = trim($request->input('username'));
    
        if (empty($input)) {
            Log::debug("Erro: O campo de utilizador está vazio.");
            throw new \Exception("Por favor, introduza o nome de utilizador ou email.");
        }
    
        // Normaliza o nome de utilizador (remove @domínio, se necessário)
        $username = str_contains($input, '@') ? explode('@', $input)[0] : $input;
        $email = $username . '@jf-parquedasnacoes.pt';
    
        Log::debug("Tentando autenticação via LDAP para: " . $username);
    
        // Tenta autenticar no LDAP
        $ldap_user = Ldap::findAndBindUserLdap($username, $request->input('password'));
    
        if (!$ldap_user) {
            Log::debug("Utilizador LDAP não autenticado: " . $username);
            throw new \Exception("Falha na autenticação LDAP. Verifique as suas credenciais.");
        }
    
        Log::debug("Utilizador LDAP autenticado com sucesso: " . $username);
    
        // Verifica se o utilizador já existe na base de dados
        $user = User::where('username', $username)
                    ->orWhere('email', $email)
                    ->whereNull('deleted_at')
                    ->where('activated', 1)
                    ->first();
    
        if (!$user) {
            Log::debug("Utilizador autenticado no LDAP, mas não encontrado na base de dados.");
            throw new \Exception("O utilizador não está registado no sistema.");
        }
    
        // Autentica o utilizador no Laravel
        Auth::login($user);
        Log::debug("Utilizador autenticado no Laravel: " . $username);
    
        return $user;
    }
    

    private function loginViaRemoteUser(Request $request)
    {
        $header_name = Setting::getSettings()->login_remote_user_header_name ?: 'REMOTE_USER';
        $remote_user = $request->server($header_name);
        if (!isset($remote_user)) {
          $remote_user = $request->server('REDIRECT_'.$header_name);
        }
        if (Setting::getSettings()->login_remote_user_enabled == '1' && isset($remote_user) && ! empty($remote_user)) {
            Log::debug("Authenticating via HTTP header $header_name.");

            $strip_prefixes = [
                // IIS/AD
                // https://github.com/snipe/Parque Seguro/pull/5862
                '\\',

                // Google Cloud IAP
                // https://cloud.google.com/iap/docs/identity-howto#getting_the_users_identity_with_signed_headers
                'accounts.google.com:',
            ];

            $pos = 0;
            foreach ($strip_prefixes as $needle) {
                if (($pos = strpos($remote_user, $needle)) !== false) {
                    $pos += strlen($needle);
                    break;
                }
            }

            if ($pos > 0) {
                $remote_user = substr($remote_user, $pos);
            }

            try {
                $user = User::where('username', '=', $remote_user)->whereNull('deleted_at')->where('activated', '=', '1')->first();
                Log::debug('Remote user auth lookup complete');
                if (! is_null($user)) {
                    Auth::login($user, $request->input('remember'));
                }
            } catch (Exception $e) {
                Log::debug('There was an error authenticating the Remote user: '.$e->getMessage());
            }
        }
    }

    /**
     * Account sign in form processing.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        if (config('app.require_saml')) {
            return view('errors.403');
        }
    
        if (Setting::getSettings()->login_common_disabled == '1') {
            return view('errors.403');
        }
    
        $validator = $this->validator($request->all());
    
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    
        $this->maxLoginAttempts = config('auth.passwords.users.throttle.max_attempts');
        $this->lockoutTime = config('auth.passwords.users.throttle.lockout_duration');
    
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
    
        $input = trim($request->input('username'));
    
        Log::debug("🔍 Tentando login para: " . $input);
    
        // Determinar se o utilizador pertence ao domínio LDAP
        $isLdapUser = str_ends_with($input, '@jf-parquedasnacoes.pt') || !str_contains($input, '@');
    
        if (!$isLdapUser) {
            Log::debug("🟢 Utilizador não pertence ao LDAP, tentando autenticação local.");
    
            if (Auth::attempt(['email' => $input, 'password' => $request->input('password'), 'activated' => 1], $request->input('remember'))) {
                $this->clearLoginAttempts($request);
                $user = auth()->user();
                $user->update(['last_login' => now()]);
                Log::info("✅ Utilizador LOCAL autenticado com sucesso: {$user->username} ({$user->email})");
    
                return redirect()->intended('/')->with('success', 'Login efetuado com sucesso.');
            }
    
            Log::debug("❌ Autenticação local falhou para: " . $input);
            return redirect()->back()->withInput()->withErrors(['username' => 'Credenciais inválidas.']);
        }
    
        // Para utilizadores LDAP, garantir que o nome não tem domínio
        if (str_contains($input, '@jf-parquedasnacoes.pt')) {
            $input = explode('@', $input)[0]; // Remove o domínio
            Log::debug("🔄 Removendo domínio para autenticação LDAP: " . $input);
        }
    
        if (Setting::getSettings()->ldap_enabled) {
            Log::debug("📡 Tentando login via LDAP.");
            try {
                $ldap_user = Ldap::findAndBindUserLdap($input, $request->input('password'));
    
                if ($ldap_user) {
                    Log::debug("🔐 Autenticação LDAP sucedida para: " . $input);
    
                    $user = User::where('username', $input)
                                ->orWhere('email', $input . '@jf-parquedasnacoes.pt')
                                ->whereNull('deleted_at')
                                ->where('activated', 1)
                                ->first();
    
                    if (!$user) {
                        Log::debug("🚫 Utilizador autenticado no LDAP, mas não encontrado na BD.");
                        throw new \Exception("O utilizador não está registado no sistema.");
                    }
    
                    Auth::login($user);
                    Log::info("✅ Utilizador LDAP autenticado com sucesso: {$user->username} ({$user->email})");
    
                    return redirect()->intended('/')->with('success', 'Login efetuado com sucesso.');
                }
            } catch (\Exception $e) {
                Log::debug("❌ Falha no login via LDAP: " . $e->getMessage());
            }
        }
    
        $this->incrementLoginAttempts($request);
        Log::debug("❌ Autenticação falhou para: " . $input);
        
        return redirect()->back()->withInput()->withErrors(['username' => 'Credenciais inválidas.']);
    }
    
    


    /**
     * Two factor enrollment page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getTwoFactorEnroll()
    {

        // Make sure the user is logged in
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', trans('auth/general.login_prompt'));
        }

        $settings = Setting::getSettings();
        $user = auth()->user();

        // We wouldn't normally see this page if 2FA isn't enforced via the
        // \App\Http\Middleware\CheckForTwoFactor middleware AND if a device isn't enrolled,
        // but let's check check anyway in case there's a browser history or back button thing.
        // While you can access this page directly, enrolling a device when 2FA isn't enforced
        // won't cause any harm.

        if (($user->two_factor_secret != '') && ($user->two_factor_enrolled == 1)) {
            return redirect()->route('two-factor')->with('error', trans('auth/message.two_factor.already_enrolled'));
        }

        $secret = Google2FA::generateSecretKey();
        $user->two_factor_secret = $secret;

        $barcode = new Barcode();
        $barcode_obj =
            $barcode->getBarcodeObj(
                'QRCODE',
                sprintf(
                    'otpauth://totp/%s:%s?secret=%s&issuer=Parque Seguro&period=30',
                    urlencode($settings->site_name),
                    urlencode($user->username),
                    urlencode($secret)
                ),
                300,
                300,
                'black',
                [-2, -2, -2, -2]
            );

        $user->saveQuietly(); // make sure to save *AFTER* displaying the barcode, or else we might save a two_factor_secret that we never actually displayed to the user if the barcode fails

        return view('auth.two_factor_enroll')->with('barcode_obj', $barcode_obj);
    }

    /**
     * Two factor code form page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getTwoFactorAuth()
    {
        // Check that the user is logged in
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', trans('auth/general.login_prompt'));
        }

        $user = auth()->user();

        // Check whether there is a device enrolled.
        // This *should* be handled via the \App\Http\Middleware\CheckForTwoFactor middleware
        // but we're just making sure (in case someone edited the database directly, etc)
        if (($user->two_factor_secret == '') || ($user->two_factor_enrolled != 1)) {
            return redirect()->route('two-factor-enroll');
        }

        return view('auth.two_factor');
    }

    /**
     * Two factor code submission
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTwoFactorAuth(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', trans('auth/general.login_prompt'));
        }

        if (! $request->filled('two_factor_secret')) {
            return redirect()->route('two-factor')->with('error', trans('auth/message.two_factor.code_required'));
        }

        $user = auth()->user();
        $secret = $request->input('two_factor_secret');

        if (Google2FA::verifyKey($user->two_factor_secret, $secret)) {
            $user->two_factor_enrolled = 1;
            $user->saveQuietly();
            $request->session()->put('2fa_authed', $user->id);

            return redirect()->route('home')->with('success', trans('auth/message.signin.success'));
        }

        return redirect()->route('two-factor')->with('error', trans('auth/message.two_factor.invalid_code'));
    }


    /**
     * Logout page.
     *
     * @param Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout is only allowed with a http POST but we need to allow GET for SAML SLO
        $settings = Setting::getSettings();
        $saml = $this->saml;
        $samlLogout = $request->session()->get('saml_logout');
        $sloRedirectUrl = null;
        $sloRequestUrl = null;
    
        // Only allow GET if we are doing SAML SLO otherwise abort with 405
        if ($request->isMethod('GET') && !$samlLogout) {
            abort(405);
        }

        if ($saml->isEnabled()) {
            $auth = $saml->getAuth();
            $sloRedirectUrl = $request->session()->get('saml_slo_redirect_url');

            if (! empty($auth->getSLOurl()) && $settings->saml_slo == '1' && $saml->isAuthenticated() && empty($sloRedirectUrl)) {
                $sloRequestUrl = $auth->logout(null, [], $saml->getNameId(), $saml->getSessionIndex(), true, $saml->getNameIdFormat(), $saml->getNameIdNameQualifier(), $saml->getNameIdSPNameQualifier());
            }

            $saml->clearData();
        }

        if (! empty($sloRequestUrl)) {
            return redirect()->away($sloRequestUrl);
        }

        $request->session()->regenerate(true);

        if ($request->session()->has('password_hash_'.Auth::getDefaultDriver())){
            $request->session()->remove('password_hash_'.Auth::getDefaultDriver());
        }

        Auth::logout();

        if (! empty($sloRedirectUrl)) {
            return redirect()->away($sloRedirectUrl);
        }

        $customLogoutUrl = $settings->login_remote_user_custom_logout_url;
        if ($settings->login_remote_user_enabled == '1' && $customLogoutUrl != '') {
            return redirect()->away($customLogoutUrl);
        }

        return redirect()->route('login')->with(['success' => trans('auth/message.logout.success'), 'loggedout' => true]);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }


    public function username()
    {
        return 'username';
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $minutes = round($seconds / 60);

        $message = trans('auth/message.throttle', ['minutes' => $minutes]);

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => $message]);
    }


    /**
     * Override the lockout time and duration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $lockoutTime = config('auth.passwords.users.throttle.lockout_duration');
        $maxLoginAttempts = config('auth.passwords.users.throttle.max_attempts');

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $maxLoginAttempts,
            $lockoutTime
        );
    }

    public function legacyAuthRedirect()
    {
        return redirect()->route('login');
    }

    public function redirectTo()
    {
        return Session::get('backUrl') ? Session::get('backUrl') : $this->redirectTo;
    }
}
