<?php
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
// Route::get('/intro', 'LandingpageController@index');
// Route::get('/', 'loginController@index')->name('login');
// Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

Route::get('/update', function () {
    return redirect('/');
});





//Cache
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cleared!";
});

//Login
Auth::routes(['verify' => true]);
//Custom User Login and Register
Route::post('register', '\Modules\User\Controllers\UserController@userRegister')->name('auth.register');
Route::post('login', '\Modules\User\Controllers\UserController@userLogin')->name('auth.login');

Route::get('how-works-host', '\App\Http\Controllers\HomeController@howWorksHost')->name('howWorksHost');

Route::get('make-it-count', '\App\Http\Controllers\HomeController@makeItCount')->name('makeItCount');

Route::post('contactSubmit', '\App\Http\Controllers\HomeController@contactSubmit')->name('contactSubmit');
Route::post('contact-host', '\App\Http\Controllers\HomeController@contactHost')->name('contactHost');

Route::get('login-as/{id}', '\Modules\User\Controllers\UserController@userLoginAs')->name('auth.loginAs');

Route::match(['GET' , 'POST'] , 'logout', '\Modules\User\Controllers\UserController@logout')->name('auth.logout');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin')->name('social.login');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack')->name('social.callback');

// Logs
Route::get('admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view']);
Route::get('fix-locations', '\App\Http\Controllers\DevController@fixLocations')->name('fixLocations');

Route::get('redirectLogin', '\Modules\User\Controllers\UserController@redirectLogin')->name('auth.redirectLogin');


Route::group(['prefix' => 'm', 'middleware' => 'pwa'], function () {

    Route::get('signin', '\App\Http\Controllers\Pwa\HomeController@signin')->name('pwa.get.signin');

    Route::get('signup', '\App\Http\Controllers\Pwa\HomeController@signup')->name('pwa.get.signup');

    Route::match(['GET' , 'POST'] , 'logout', '\App\Http\Controllers\Pwa\HomeController@logout')->name('auth.logout');

    Route::get('home', '\App\Http\Controllers\Pwa\HomeController@home')->name('pwa.get.home');

    Route::get('space-details/{id}', '\App\Http\Controllers\Pwa\HomeController@spaceDeatils')->name('pwa.get.spaceDeatils');

    Route::get('booking-details/{id}', '\App\Http\Controllers\Pwa\HomeController@bookingDetails')->name('pwa.get.bookingDetails');


    Route::get('space-by-category/{id}', '\App\Http\Controllers\Pwa\HomeController@spaceByCategory')->name('pwa.get.spaceByCategory');

    Route::get('host/{id}', '\App\Http\Controllers\Pwa\HomeController@spaceByHost')->name('pwa.get.host');

    Route::get('my-favourites', '\App\Http\Controllers\Pwa\HomeController@myFavourites')->name('pwa.get.myFavourites');

    Route::get('cafes', '\App\Http\Controllers\Pwa\HomeController@cafesList')->name('pwa.get.cafesList');


    Route::get('search', '\App\Http\Controllers\Pwa\HomeController@search')->name('pwa.get.search');

    Route::get('profile', '\App\Http\Controllers\Pwa\HomeController@profile')->name('pwa.get.profile');

    Route::get('logout', '\App\Http\Controllers\Pwa\HomeController@logout')->name('pwa.logout');

    Route::post('update-profile-info', '\App\Http\Controllers\Pwa\HomeController@updateProfileInfo')->name('pwa.updateProfileInfo');
    Route::post('update-networking-info', '\App\Http\Controllers\Pwa\HomeController@updateNetworkingInfo')->name('pwa.updateNetworkingInfo');
    Route::post('update-social-info', '\App\Http\Controllers\Pwa\HomeController@updateSocialInfo')->name('pwa.updateSocialInfo');

    Route::post('update-notification-profile-details', '\App\Http\Controllers\Pwa\HomeController@updateNotificationProfileDetails')->name('pwa.updateNotificationProfileDetails');

    Route::post('support-submit', '\App\Http\Controllers\Pwa\HomeController@supportSubmit')->name('pwa.supportSubmit');


    Route::get('booking/{code}/checkout','\Modules\Booking\Controllers\BookingController@checkout');

    Route::get('booking-list', '\App\Http\Controllers\Pwa\HomeController@bookingList')->name('pwa.bookingList');

    Route::get('view-all-nearby-spaces', '\App\Http\Controllers\Pwa\HomeController@viewAllNearbySpaces')->name('pwa.viewAllNearbySpaces');




});










