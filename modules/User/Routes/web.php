<?php

use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);
Route::group(['prefix' => 'user', 'middleware' => ['auth', 'verified']], function () {
    Route::match(['get'], '/dashboard', 'UserController@dashboard')->name("vendor.dashboard");

    Route::match(['get'], '/earning-stats', 'UserController@earningStats')->name("vendor.earningStats");

    Route::match(['get'], '/inbox', 'UserController@inbox')->name("user.inbox");
    Route::match(['get'], '/calendar', 'UserController@calendar')->name("vendor.calendar");
    Route::match(['get'], '/clients', 'UserController@clients')->name("vendor.clients");
    Route::match(['get'], '/reports', 'UserController@reports')->name("vendor.reports");
    Route::match(['get'], '/withdraw', 'UserController@withdraw')->name("vendor.withdraw");

    Route::post('/reloadChart', 'UserController@reloadChart');
    Route::get('/user-dashboard', 'UserController@userDashboard')->name("user.dashboard");
    Route::get('/my/calendar', 'UserController@userCalendar')->name("user.calendar");
    Route::get('/user-dashboard-data', 'UserController@userDashboardData')->name("user.dashboardData");
    Route::get('/vendor-dashboard-data', 'UserController@vendorDashboardData')->name("user.vendorDashboardData");
    Route::post('/user/move-booking-date', 'UserController@moveBooking')->name("user.moveBooking");

    Route::get('/p/profile/{id}', 'UserController@publicProfile')->name("user.profile.publicProfile");

    Route::get('/profile', 'UserController@profile')->name("user.profile.index");
    Route::post('/profile', 'UserController@profileUpdate')->name("user.profile.update");
    Route::get('/profile/change-password', 'UserController@changePassword')->name("user.change_password");
    Route::post('/profile/change-password', 'UserController@changePasswordUpdate')->name("user.change_password.update");
    Route::get('/bookings-details', 'UserController@bookingsDetails')->name("user.bookings.details");
    Route::post('/bookings-data-table', 'BookingsTableController')->name("user.bookings.datatable");
    Route::get('/booking-details/{id}', 'UserController@singleBookingDetails')->name("user.single.booking.detail");
    Route::get('/booking-checkinout-settings', 'UserController@bookingcheckinoutsettings')->name("user.checkinout.settings");

    Route::get('/enquiry-report', 'UserController@enquiryReport')->name("vendor.enquiry_report");
    Route::get('/enquiry-report/bulkEdit/{id}', 'UserController@enquiryReportBulkEdit')->name("vendor.enquiry_report.bulk_edit");

    Route::post('/get-boooking-detail', 'UserController@getBooking')->name('user.bookings.get.detail');
    Route::post('/boooking-cancel', 'UserController@cancelBooking')->name('user.bookings.get.cancel');

    Route::post('/wishlist', 'UserWishListController@handleWishList')->name("user.wishList.handle");
    Route::get('/wishlist', 'UserWishListController@index')->name("user.wishList.index");
    Route::get('/wishlist/remove', 'UserWishListController@remove')->name("user.wishList.remove");

    Route::get('/favourites', 'UserController@favourites')->name("user.favourites");
    Route::post('/favourites-data-table', 'UserController@favourites_datatable')->name("user.favourites.datatable");
    Route::get('/remove-favourite/{id}', 'UserController@removeFavourite')->name('user.favourite.remove');

    Route::post('/bookings-search', 'UserController@bookingsSearch')->name('user.bookings.search');

    Route::group(['prefix' => 'verification'], function () {
        Route::match(['get'], '/', 'VerificationController@index')->name("user.verification.index");
        Route::match(['get'], '/update', 'VerificationController@update')->name("user.verification.update");
        Route::post('/store', 'VerificationController@store')->name("user.verification.store");
        Route::post('/send-code-verify-phone', 'VerificationController@sendCodeVerifyPhone')->name("user.verification.phone.sendCode");
        Route::post('/verify-phone', 'VerificationController@verifyPhone')->name("user.verification.phone.field");
    });

    Route::group(['prefix' => '/booking'], function () {
        Route::get('{code}/invoice', 'BookingController@bookingInvoice')->name('user.booking.invoice');
        Route::get('{id}/email', 'BookingController@bookingEmail')->name('user.booking.email');
        Route::post('bulk-invoice', 'BookingController@bulkBookingInvoice')->name('user.booking.bulk.invoice');
        Route::post('export-invoices', 'BookingController@exportBookings')->name('user.booking.export');
        Route::get('{code}/ticket', 'BookingController@ticket')->name('user.booking.ticket');
        Route::get('archive/{id}', 'BookingController@archive')->name('user.booking.archive');
        Route::get('checkin/{id}', 'BookingController@checkin')->name('user.booking.checkin');
        Route::get('checkout/{id}', 'BookingController@checkout')->name('user.booking.checkout');
        Route::post('guestcheckinpost/{id}', 'BookingController@guestcheckinpost')->name('user.booking.guestcheckinpost');
        Route::post('guestcheckoutpost/{id}', 'BookingController@guestcheckoutpost')->name('user.booking.guestcheckoutpost');
        Route::get('guestcheckin/{id}', 'BookingController@guestcheckin')->name('user.booking.guestcheckin');
        Route::get('guestcheckout/{id}', 'BookingController@guestcheckout')->name('user.booking.guestcheckout');
        Route::post('sendsmspre', 'BookingController@sendsmspre')->name('user.booking.sendsmspre');
        Route::post('sendsmsarrival', 'BookingController@sendsmsarrival')->name('user.booking.sendsmsarrival');
        Route::post('sendemaillatecheckin', 'BookingController@sendemaillatecheckin')->name('user.booking.sendemaillatecheckin');
        Route::post('sendsmspredeparture', 'BookingController@sendsmspredeparture')->name('user.booking.sendsmspredeparture');
        Route::post('sendsmslatecheckout', 'BookingController@sendsmslatecheckout')->name('user.booking.sendsmslatecheckout');
        Route::get('sendsmsauto', 'BookingController@sendsmsauto')->name('user.booking.sendsmsauto');
        Route::post('sendbooking', 'BookingController@sendbooking')->name('user.booking.sendbooking');
        Route::post('statuschange', 'BookingController@statuschange')->name('user.booking.statuschange');
        Route::post('completebooking', 'BookingController@completebooking')->name('user.booking.completebooking');
        Route::post('extendbooking', 'BookingController@extendbooking')->name('user.booking.extendbooking');
        Route::post('contactguest', 'BookingController@contactguest')->name('user.booking.contactguest');
        Route::post('contactSubmit', 'BookingController@contactSubmit')->name('user.booking.contactSubmit');
        Route::post('contactHost', 'BookingController@contactHost')->name('user.booking.contactHost');
        Route::post('thankyouemail', 'BookingController@thankyouemail')->name('user.booking.thankyouemail');
        Route::post('user.booking.verifySelectedTimes1', 'BookingController@verifySelectedTimes1')->name('user.booking.verifySelectedTimes1');
        Route::post('user.booking.verifySelectedTimes2', 'BookingController@verifySelectedTimes2')->name('user.booking.verifySelectedTimes2');
        Route::post('user.booking.availableDates2', 'BookingController@availableDates2')->name('user.booking.availableDates2');
        Route::post('user.booking.reschedule', 'BookingController@reschedule')->name('user.booking.reschedule');
    });

    Route::match(['get'], '/upgrade-vendor', 'UserController@upgradeVendor')->name("user.upgrade_vendor");

    Route::get('wallet', 'WalletController@wallet')->name('user.wallet');
    Route::any('transaction-history', 'WalletController@transactionHistory')->name('user.transactionHistory');
    Route::any('transaction-details/{uuid}', 'WalletController@transactionDetails')->name('user.transactionDetails');

    Route::get('wallet/buy', 'WalletController@buy')->name('user.wallet.buy');
    Route::post('wallet/buyProcess', 'WalletController@buyProcess')->name('user.wallet.buyProcess');

    Route::get('wallet/withdraw', 'WalletController@withdraw')->name('user.wallet.withdraw');
    Route::post('wallet/process-withdraw', 'WalletController@prcoessWithdraw')->name('user.wallet.processWithdraw');

    Route::get('chat', 'ChatController@index')->name('user.chat');
});

Route::group(['prefix' => config('chatify.path'), 'middleware' => 'auth'], function () {
    Route::get('/', 'ChatController@iframe')->name(config('chatify.path'));
    Route::post('search', 'ChatController@search')->name('search');
    Route::post('getContacts', 'ChatController@getContacts')->name('contacts.get');
    Route::post('idInfo', 'ChatController@idFetchData');
    Route::post('sendMessage', 'MessageController@send')->name('send.message');
});


Route::get('main-search', 'UserController@mainSearch')->name('main.search');

Route::group(['prefix' => 'profile'], function () {
    Route::match(['get'], '/{id}', 'ProfileController@profile')->name("user.profile");
    Route::match(['get'], '/{id}/reviews', 'ProfileController@allReviews')->name("user.profile.reviews");
    Route::match(['get'], '/{id}/services', 'ProfileController@allServices')->name("user.profile.services");
});

//Newsletter
Route::post('newsletter/subscribe', 'UserController@subscribe')->name('newsletter.subscribe');
Route::group(['prefix' => 'gateway'], function () {
    Route::get('/confirm/{gateway}', 'NormalCheckoutController@confirmPayment')->name('gateway.confirm');
    Route::get('/cancel/{gateway}', 'NormalCheckoutController@cancelPayment')->name('gateway.cancel');
    Route::get('/confirm/extend/two_checkout_gateway/{id}', 'NormalCheckoutController@confirmExtendPayment')->name('gateway.extend.confirm');
    Route::get('/cancel/extend/two_checkout_gateway/{id}', 'NormalCheckoutController@cancelExtendPayment')->name('gateway.extend.cancel');
    Route::get('/info', 'NormalCheckoutController@showInfo')->name('gateway.info');
    Route::get('/update-amount', 'NormalCheckoutController@updateAmount')->name('gateway.update');
    Route::get('/update-amount-space', 'NormalCheckoutController@updateAmountSpace')->name('gateway.update.space');
    Route::get('/update-extend-amount', 'NormalCheckoutController@updateExtendAmount')->name('gateway.extend.update');
});
