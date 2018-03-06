<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Userwallet;
use App\Models\User_wallet_history;
use App\Models\User_transaction_history;
use App\Models\Usernotification;
use Illuminate\Pagination\Paginator;

use Auth;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    public function index()
    {
        //
        $title = 'Dashboard';
        $content = 'contents.home';
        $user = Auth::user();
        $user_id = $user->id;
        $userwallet = Userwallet::where('user_id', '=', $user_id)->first();
        $userwallethistory = User_wallet_history::where('wallet_id', '=', $userwallet->id)->orderBy('transaction_date', 'desc')->paginate(10);
        $usertransactionhistory = DB::table('user_transaction_history')
                ->select('user_transaction_history.*', DB::raw('(select name from users where id = user_transaction_history.target_user_id) as target_name'))
                ->where('user_transaction_history.user_id', '=', $user_id)->orderBy('transaction_date', 'desc')->paginate(10);
        $allnotification = DB::table('user_notification_log')
                ->select(
                    'user_notification_log.*', 
                    DB::raw('(select name from users where id = user_notification_log.user_id) as recipient_name'), 
                    DB::raw('(select name from users where id = user_notification_log.target_user_id) as target_name')
                )
                ->where('user_notification_log.user_id', '=', $user_id)
                ->orderBy('updated_at', 'desc')->get();

        $newnotification = Usernotification::where('user_id', '=', $user_id)->where('read','=', '0');

        return view('dashboard', compact('title', 'content', 'userwallet', 'userwallethistory', 'newnotification', 'usertransactionhistory', 'allnotification'));
    }

    public function updatenotif(Request $request)
    {
        $user_id = $request->uid;
        $newnotification = DB::table('user_notification_log')->where('user_id', '=', $user_id)->update(array('read' => 1));
        echo json_encode(array('code'=>200, 'result'=>'ok'));
    }
}
