<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Userwallet;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\View;
//use Illuminate\Support\Facades\Hash; // used for password hash in laravel

use Auth;
use Hash;
use Validator;
use Redis;
use View;
use DB;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Auth::check()) {
            // The user is logged in...
            return redirect('dashboard');
        }

        $this->middleware('guest');
    }

    protected function guard()
    {
        return Auth::guard('guard-name');
    }

    public function index()
    {
        if (Auth::check()) {
            // The user is logged in...
            return redirect('dashboard');
        }

        $title = 'Sign Up Form';
        $content = 'contents.register';

        return view('index', compact('title','content'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    /*protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }*/

    public function register(Request $request)
    {   
        $passHash = "";
        if($request->reg_password != "")
            $passHash = sha1(md5(strtolower($request->reg_password)));             

        $data_reg  = array(
            'name' => $request->fullname,
            'email'     => strtolower($request->reg_email),
            'password'  => $request->reg_password,
            'password_confirmation' => $request->password_confirmation
        );

        $validator_result = $this->validator($data_reg);

        if ($validator_result->fails()) {
            return redirect('register')
                ->withErrors($validator_result, 'register') // send back all errors to the login form
                ->withInput($request->except(['reg_password', 'reg_password_confirmation'])); // send back the input (not the password) so that we can repopulate the form
        } else {
            DB::beginTransaction();

            $data_user = new User;
            $data_user->name = $request->fullname;
            $data_user->email = $request->reg_email;
            $data_user->password = Hash::make($passHash);

            if($data_user->save()) {
                $data_wallet = new Userwallet;

                $invalid_account = TRUE;
                /* generate the user wallet */
                $string_allowed = '0123456789';                
                while($invalid_account) {
                    $virtual_account = "1000";
                    
                    for($x=1;$x<8;$x++){ 
                        $randNum = rand(0, 9); 
                        $virtual_account .= "$randNum";
                    } 

                    $find_wallet = $data_wallet->where('wallet_account', $virtual_account)->get()->count();

                    if($find_wallet == 0) {
                        $invalid_account = false;
                    }
                }

                //$queries = DB::getQueryLog();
                $data_wallet->user_id = $data_user->id;
                $data_wallet->wallet_account = $virtual_account;
                $data_wallet->wallet_password = $data_user->email;
                $data_wallet->wallet_amount = 0;
                $data_wallet->is_active = 1;
               
                if($data_wallet->save()) {
                    DB::commit();
                    //add email verification here if you want
                    /**/

                    return redirect('login')->with('success_message', 'Sign Up Success. You can now login with your new account');
                } else {
                    DB::rollback();      
                }
            }            
        }
    }
}
