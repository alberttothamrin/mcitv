<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\View;
//use Illuminate\Support\Facades\Hash; // used for password hash in laravel

use Auth;
use Hash;
use Validator;
use Redis;
use View;

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
    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected function guard()
    {
        return Auth::guard('guard-name');
    }

    public function __construct()
    {        
        $this->middleware('guest', ['except' => ['logout', 'dologout']]);
    }


    /* by default laravel using email as login.
    use this if you want use username as login instead of email 
    public function username()
    {
        return 'username';
    }
    */

    public function index()
    {
        if (Auth::check()) {
            // The user is logged in...
            return redirect('dashboard');
        }

        $title = 'Login Form';
        $content = 'contents.login';
        return view('index', compact('title','content'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|',
        ]);
    }

    public function about()
    {
        $title = 'About Me';
        $content = 'contents.about';
           
        return view('index', compact('title', 'content'));
    }

    public function help()
    {
        $title = 'Help';
        $content = 'contents.help';
           
        return view('index', compact('title', 'content'));
    }

    public function dologout(Request $request)
    {
        Auth::logout(); // logs out the user
        return redirect('login')->with('success_message', 'Logout success. Please come back soon');
    }

    public function dologin(Request $request)
    {

        $input = $request->all();
        // run the validation rules on the inputs from the form
        $validator_result = $this->validator($input);

        if ($validator_result->fails()) {
            return redirect('login')
                ->withErrors($validator_result, 'login') // send back all errors to the login form
                ->withInput($request->except(['password'])); // send back the input (not the password) so that we can repopulate the form
        } else {
            $passHash = sha1(md5(strtolower($request->password)));

            // create our user data for the authentication
            $credentials  = array(
                'email'     => strtolower($request->email),
                'password'  => $passHash
            );

            // attempt to do the login
            if (Auth::attempt($credentials)) {                
                Redis::set('user_id', Auth::user()->id);                
                //echo Redis::get('name');
                return redirect('dashboard');
            } else {        
                //$request->flashOnly(['email']);
                //$request->flashExcept('password');
                // validation not successful, send back to form 
                return redirect('login')->withErrors(['invalidLogin'=>'Invalid User Credential']);
            }

        }
    }
}
