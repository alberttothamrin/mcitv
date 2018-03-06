<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Userwallet;
use App\Models\User_wallet_history;
use App\Models\User_transaction_history;
use App\Models\Usernotification;

use Auth;
use Validator;
use View;
use DB;

class TransactionController extends Controller
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

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'account' => 'required|string|min:11',
            'amount' => 'required|numeric|min:10000|max:999999999'
        ]);
    }

    public function dotransfer(Request $request)
    {
        $data_reg  = array(
            'account' => $request->tgt_virt_account,
            'amount'=> $request->amt_transfer
        );

        $validator_result = $this->validator($data_reg);

        if ($validator_result->fails()) {
            return redirect('dashboard')
                ->withErrors($validator_result, 'transfer');
        } else {
            DB::beginTransaction();
            $current_user = Auth::user();
            $current_user_wallet = Userwallet::where('user_id', '=', $current_user->id)->first();

            $tgt_user = DB::table('user_wallet')
                ->join('users', 'user_wallet.user_id', '=', 'users.id')
                ->select('users.*', 'user_wallet.id as wallet_id')
                ->where('user_wallet.user_id', '!=', $current_user->id) //never trf to self 
                ->where('user_wallet.wallet_account', '=', $request->tgt_virt_account)
            ->get()->first();

            $trans_date = date("Y-m-d H:i:s");
            $trans_history = new User_transaction_history;
            $trans_history->user_id = $current_user->id;
            $trans_history->target_user_id = $tgt_user->id;
            $trans_history->history_type = "Transfer";
            $trans_history->transaction_amount = $request->amt_transfer;
            $trans_history->transaction_date = $trans_date;

            $trans_history_tgt = new User_transaction_history;
            $trans_history_tgt->user_id = $tgt_user->id;
            $trans_history_tgt->target_user_id = $current_user->id;
            $trans_history_tgt->history_type = "Transfered";
            $trans_history_tgt->transaction_amount = $request->amt_transfer;
            $trans_history_tgt->transaction_date = $trans_date;

            if($trans_history->save()) {
                $wallet_id = $tgt_user->wallet_id;

                $current_user_wallet->wallet_amount = $current_user_wallet->wallet_amount - $request->amt_transfer;
                $affectedRows = $current_user_wallet->save();

                $target_user_wallet = Userwallet::where('id', '=', $wallet_id)->first();
                $target_user_wallet->wallet_amount = $target_user_wallet->wallet_amount + $request->amt_transfer;
                $affectedRows = $target_user_wallet->save();
                
                $history = new User_wallet_history;
                $history->wallet_id = $current_user_wallet->id;
                $history->history_type = 'deduction';
                $history->transaction_amount = $request->amt_transfer;
                $history->transaction_date = $trans_date;
                $history->save();

                $history = new User_wallet_history;
                $history->wallet_id = $wallet_id;
                $history->history_type = 'transfer';
                $history->transaction_amount = $request->amt_transfer;
                $history->transaction_date = $trans_date;
                $history->save();

                $log = new Usernotification;
                $log->user_id = $current_user->id;
                $log->target_user_id = $tgt_user->id;
                $log->notification_type = 'transfer';
                $log->notification_detail = 'You just transfer to :target_user_name: on '. date("j F Y H:i:s") .' for Rp ' .number_format($request->amt_transfer, 2);
                $log->notification_url =url('dashboard#transactionlog');
                $log->save();

                $log = new Usernotification;
                $log->user_id = $tgt_user->id;
                $log->target_user_id = $current_user->id;
                $log->notification_type = 'transfer';
                $log->notification_detail = 'You just received a transfer from :recipient_user_name: to your account on '. date("j F Y H:i:s") .' with total amount :' .$request->amt_transfer;
                $log->notification_url =url('dashboard#transactionlog');
                $log->save(); 

                DB::commit();

                return redirect('dashboard')->with('success_transfer', 'Your transfer is success with amount : '. $request->amt_transfer);               
            }  else {
                DB::rollback();      
            }
        }
    }

    public function canceltransfer(Request $request)
    {
        if ($request->check == "" || base64_decode($request->check) == 0 || !is_numeric(base64_decode($request->check))) {
            return redirect('dashboard#transactionlog')->with('error_reversal', 'You are not allowed / authorized to do this action'); 
        } else {
            $trans_id = base64_decode($request->check);
            $trans_history = user_transaction_history::where('id', '=', $trans_id)->first();

            $t1 = strtotime($trans_history->transaction_date);
            $t2 = time();

            $dtd = new \stdClass();
            $dtd->total_sec = abs($t2-$t1);
            $dtd->total_hour = floor($dtd->total_sec/3600);
            if($dtd->total_hour >= 6) {
                return redirect('dashboard#transactionlog')->with('error_reversal', 'Transaction is already completed and can\'t be reversed'); 
            }

            $user_wallet = Userwallet::where('user_id', '=', $trans_history->user_id)->first();
            $target_user_wallet = Userwallet::where('user_id', '=', $trans_history->target_user_id)->first();

            if($target_user_wallet->wallet_amount < 100000) {
                return redirect('dashboard#transactionlog')->with('error_reversal', 'The balance of target user is not adequate to do the reversal transfer'); 
            }
            
            DB::beginTransaction();
            /* return the money */
            try {
                $user_wallet->wallet_amount = $user_wallet->wallet_amount + $trans_history->transaction_amount;
                $affectedRows = $user_wallet->save();

                $target_user_wallet->wallet_amount = $target_user_wallet->wallet_amount - $trans_history->transaction_amount;
                $affectedRows = $target_user_wallet->save();
                
                $history = new User_wallet_history;
                $history->wallet_id = $user_wallet->id;
                $history->history_type = 'reversal';
                $history->transaction_amount = $trans_history->transaction_amount;
                $history->transaction_date = date("Y-m-d H:i:s");
                $history->save();

                $history = new User_wallet_history;
                $history->wallet_id = $target_user_wallet->id;
                $history->history_type = 'reversed';
                $history->transaction_amount = $trans_history->transaction_amount;
                $history->transaction_date = date("Y-m-d H:i:s");
                $history->save();

                $log = new Usernotification;
                $log->user_id = $trans_history->user_id;
                $log->target_user_id = $trans_history->target_user_id;
                $log->notification_type = 'transfer reversal';
                $log->notification_detail = 'You just reversed transfer from :target_user_name: on '. date("j F Y H:i:s") .' for Rp ' .number_format($trans_history->transaction_amount, 2);
                $log->notification_url =url('dashboard#transactionlog');
                $log->save();

                $log = new Usernotification;
                $log->user_id = $trans_history->target_user_id;
                $log->target_user_id = $trans_history->user_id;
                $log->notification_type = 'transfer';
                $log->notification_detail = ':recipient_user_name: just request a reversal transfer from your account on '. date("j F Y H:i:s") .' with total amount :' .$trans_history->transaction_amount;
                $log->notification_url =url('dashboard#transactionlog');
                $log->save(); 

                $trans_history->is_reversed = 1;
                $trans_history->save();

                DB::commit();
                return redirect('dashboard#transactionlog')->with('success_reversal', 'The reversal transfer request is success'); 
            } catch (any $e){
                DB::rollback();
                return redirect('dashboard#transactionlog')->with('error_reversal', 'Error on reversal transfer request. Check with your administrator'); 
            }

            
        } 
    }
}
