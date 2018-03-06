<div class="col-sm-12 col-md-3 col-lg-2 sidebar">
    <div id="side-profile" class="top-user profile">
        <div class="user-display clear">
            <div class="span3 pull-left mt-20 mb-5">                
                <span class="glyphicon glyphicon-user avatar"></span>
            </div>

            <div class="user-detail">
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-data text-capitalize">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="user-deposit" data-amt="{{$userwallet->wallet_amount}}">
                            <span class="glyphicon glyphicon-piggy-bank" title="Balance Amount"></span> Rp {{number_format($userwallet->wallet_amount, 2)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="side-topup" class="topup-saldo">
        <h4>Wallet Topup</h4>

        @if( Session::has( 'success_topup' ))
        <div class="alert alert-success">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get( 'success_topup' ) }}
        </div>
        @endif
        @if($errors->topup->has('nominal_topup'))
            <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ $errors->topup->first('nominal_topup') }}    
            </div>
        @endif

        <form method="post" action="{{route('wallet.topup')}}" accept-charset="UTF-8">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <label>Nominal</label>
                    <input type="text" name="nominal_topup" class="form-control number" required value="0" placeholder="nominal topup">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btn-xs" type="submit">Save</button>
                </div>
            </div>
            * only demo, no verification added
        </form>
    </div>
    <hr>
    <div id="side-transfer" class="transfer-saldo">
        <h4>Transfer Money</h4>
        @if( Session::has( 'success_transfer' ))
        <div class="alert alert-success">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get( 'success_transfer' ) }}
        </div>
      @endif

        @if($errors->transfer->has('error')) || $errors->transfer->has('account')) || $errors->transfer->has('amount'))
            <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ $errors->transfer->first('error') }}    
              {{ $errors->transfer->first('account') }}    
              {{ $errors->transfer->first('amount') }}    
            </div>
        @endif

        <form method="post" action="{{route('transfer.submit')}}" accept-charset="UTF-8">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <label>Target Virtual Account Number</label>
                    <input type="text" id="tgt_virt_account" name="tgt_virt_account" class="form-control number" required value="" onchange="javascript: check_va(this.value, '{{ Auth::user()->id }}', $('meta[name=\'csrf-token\']').attr('content'), '{{url("/")}}')">
                    <div class="loader" id="loader_va"></div>
                    <div class="check_va_result"></div>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <label>Amount</label>
                    <input type="text" id="amt_transfer" name="amt_transfer" class="form-control number" required value="0" onchange="javascript: check_amt(this.value, '{{ Auth::user()->id }}', $('meta[name=\'csrf-token\']').attr('content'), '{{url("/")}}')">
                    <div class="loader" id="loader_amt"></div>
                    <div class="check_amt_result"></div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <button id="transferBtn" class="btn btn-primary btn-xs" type="submit">Send</button>
                </div>
            </div>
            * only demo, no verification added
        </form>
    </div>
</div>

<div class="col-sm-12 col-md-8 col-lg-9 main">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills" role="tablist">
              <li role="presentation" class="active"><a href="#walletlog" class="link-tab" data-toggle="tab">Topup History <sup><span class="badge">{{$userwallethistory->count()}}</span></sup></a></li>
              <li role="presentation"><a href="#transactionlog" class="link-tab" data-toggle="tab">Transaction History <sup><span class="badge">{{$usertransactionhistory->count()}}</span></sup></a></li>          
            </ul>
        </div>        
    </div>
    <hr>
    <div class="row">
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="walletlog">
                <!--<div class="pull-right row">
                    <form method="post" action="" accept-charset="UTF-8">
                        @csrf
                        <input type="text" name="query_search"> <button type="submit">Search</button>
                    </form>
                </div>-->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userwallethistory as $history)
                            <tr>
                                <td>{{$history->history_type}}</td>
                                <td>{{number_format($history->transaction_amount, 2)}}</td>
                                <td>{{$history->transaction_date}}</td>
                            </tr>    
                        @endforeach
                        
                    </tbody>
                </table>
                {{ $userwallethistory->fragment('walletlog')->links() }}
            </div>
            <div class="tab-pane active" id="transactionlog">
                @if( Session::has( 'error_reversal' ))
                    <div class="alert alert-danger">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      {{ Session::get( 'error_reversal' ) }}
                    </div>
                @endif
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Detail</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usertransactionhistory as $history)
                            <tr>
                                <td>{{$history->history_type}}</td>
                                <td>You made a transfer to {{$history->target_name}} for Rp {{number_format($history->transaction_amount, 2)}}</td>
                                <td>{{$history->transaction_date}}</td>
                                <td>
                                    @php
                                        $t1 = strtotime($history->transaction_date);
                                        $t2 = time();

                                        $dtd = new stdClass();
                                        $dtd->total_sec = abs($t2-$t1);
                                        $dtd->total_hour = floor($dtd->total_sec/3600);
                                    @endphp

                                    @if ($dtd->total_hour < 6)
                                        @if ($history->is_reversed == 0)
                                            <a href="{{route('transfer.cancellation', ['check' =>base64_encode($history->id)])}}" class="btn"><i class="glyphicon glyphicon-repeat"></i> Reversal Transfer</a>
                                        @else 
                                            Transfer Reversed
                                        @endif
                                    @else
                                        <b>Completed</b>
                                    @endif
                                </td>
                            </tr>    
                        @endforeach                        
                    </tbody>
                </table>
                {{ $usertransactionhistory->fragment('transactionlog')->links() }}
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">My Notification</h4>
      </div>
      <div class="modal-body">
        <table class="table">
            <tbody>
                @foreach ($allnotification as $notif)
                    <tr>
                    @php 
                        $detail = str_replace(":target_user_name:", $notif->target_name, $notif->notification_detail);
                        $detail = str_replace(":recipient_user_name:", $notif->target_name, $detail);
                    @endphp
                        <td>{{$detail}}</td>                        
                    </tr>    
                @endforeach
                
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>