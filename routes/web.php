<?php
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'Auth\LoginController@index')->name('root');
Route::get('nofeature', function() {
	echo 'Function not implemented yet';
})->name('not-exists');

Route::get('about-me', 'Auth\LoginController@about')->name('about');
Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('register', 'Auth\RegisterController@index')->name('register');
Route::post('login', 'Auth\LoginController@dologin');
Route::post('register', 'Auth\RegisterController@register');

Route::post('logout', 'Auth\LoginController@dologout')->name('do.logout');
Route::post('/resetPassword', function () {
    return view('login');
});

Route::group(['middleware' => 'auth'], function(){
	Route::resources([
	    'account' => 'AccountController',
	    'dashboard' => 'DashboardController',
	    'transfer' => 'TransactionController',
	    'wallet' => 'UserWalletController'
	]);

	Route::post('topup', 'UserWalletController@topup')->name('wallet.topup');
	Route::post('topup', 'UserWalletController@topup')->name('wallet.topup');
	Route::post('transfer', 'TransactionController@transfer')->name('transfer');
	Route::post('do-transfer', 'TransactionController@dotransfer')->name('transfer.submit');
	Route::get('transfercancellation/check/{check}', 'TransactionController@canceltransfer')->name('transfer.cancellation');
	Route::post('getVAccount', 'UserWalletController@checkaccount');
	Route::post('getVAmount', 'UserWalletController@checkamount');
	Route::post('checknotif', 'DashboardController@updatenotif');
});

/*Route::get('newuser', function(){
	$user = new App\Models\User;
	$rand = rand(0,100);
	$datas = array('name'=>'Test'.$rand, 'email' => 'Testingonly'.$rand.'@test.com', 'new_password' => "12345");
	$user->newUser($datas);
});*/
//Auth::routes();
/*
Route::get('/home', 'HomeController@index')->name('home');*/
