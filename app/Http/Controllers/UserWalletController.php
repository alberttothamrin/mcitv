<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Userwallet;
use App\Models\User_wallet_history;
use App\Models\Usernotification;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\View;
//use Illuminate\Support\Facades\Hash; // used for password hash in laravel

use Auth;
use Validator;
use View;
use DB;

class UserWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
            'nominal_topup' => 'required|string|numeric|min:10000|max:999999999',
        ]);
    }

    public function topup(Request $request)
    {        
        $data_reg  = array(
            'nominal_topup' => $request->nominal_topup
        );

        $validator_result = $this->validator($data_reg);

        if ($validator_result->fails()) {
            return redirect('dashboard')
                ->withErrors($validator_result, 'topup');
        } else {
            DB::beginTransaction();

            $user = Auth::user();
            $user_id = $user->id;
            $data_wallet = Userwallet::where('user_id', '=', $user_id)->first();
            $data_wallet->wallet_amount = $data_wallet->wallet_amount + $request->nominal_topup;
            $affectedRows = $data_wallet->save();

            if ($affectedRows) {
                $wallet_id = $data_wallet->id;
                
                $history = new User_wallet_history;
                $history->wallet_id = $wallet_id;
                $history->history_type = 'topup';
                $history->transaction_amount = $request->nominal_topup;
                $history->transaction_date = date('Y-m-d H:i:s');
               
                $history->save();
                    
                $log = new Usernotification;
                $log->user_id = $user_id;
                $log->target_user_id = $user_id;
                $log->notification_type = 'topup';
                $log->notification_detail = 'You just topup on '. date("j F Y H:i:s") .' with total amount :' .$request->nominal_topup;
                $log->notification_url =url('dashboard#walletlog');
                $log->save();

                DB::commit();
                //add email verification here if you want
                /**/

                return redirect('dashboard')->with('success_topup', 'Your topup with amount : '. $request->nominal_topup .' is success');
            } else {
                DB::rollback();      
            }   
        }
    }

    public function checkamount(Request $request)
    {
        if($request->uid == "") {
            echo json_encode(array('error' => 'Account Not Found'));
        } else {
            $data_wallet = Userwallet::where('user_id', '=', $request->uid)->first();
            $leftBalance = $data_wallet->wallet_amount - $request->expense;
            if ($leftBalance >= 0) {
                echo json_encode(array('code'=>200, 'result' => number_format($leftBalance, 2)));
            } else {
                echo json_encode(array('code'=>301, 'error' => 'Not enough balance'));
            }
        }
    }
    public function checkaccount(Request $request)
    {
        if($request->account == "") {
            echo json_encode(array('error' => 'Account Not Found'));
        } else {
            /*join in join / advanced join
            $target_user = DB::table('user_wallet')
                ->join('users', function ($join) {
                $join->on('user_wallet.user_id', '=', 'users.id');
                })
                ->where('user_wallet.user_id', '!=', $request->uid) //never trf to self 
                ->where('user_wallet.wallet_account', '=', $request->account)
            ->get();*/

            $target_user = DB::table('user_wallet')
                ->join('users', 'user_wallet.user_id', '=', 'users.id')
                ->select('users.name', 'user_wallet.*')
                ->where('user_wallet.user_id', '!=', $request->uid) //never trf to self 
                ->where('user_wallet.wallet_account', '=', $request->account)
            ->get();

            if($target_user->count()) {
                echo json_encode(array('code'=>200, 'result' => $target_user->first()->name));
            } else {
                echo json_encode(array('code'=>304, 'error' => 'Account Not Found'));
            }
        }
    }
}
