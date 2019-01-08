<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/set-session';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        // if (env('APP_DOBLE_SIGNIN_URL')) {
        //     $user = auth()->user();
        //     header('Location: '.env('APP_DOBLE_SIGNIN_URL').'/'.$user->id.'/'.$user->remember_token);
        //     die();
        // }

        return $this->redirectTo;
        // TODO: Move this if up after the login issue is solved
        if (Auth::user()->hasRole('Administrator')) {
            return '/admin/dashboard';
        }
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        
        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath())->with('success_login', 'Success login');
    }
}
