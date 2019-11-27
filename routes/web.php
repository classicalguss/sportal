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

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    //Artisan::call('db:seed', ['--class' => 'UsersTableSeeder']);
    return view('index');
});

Route::get('/locale/{locale}', function($locale){
    if (array_key_exists($locale, Config::get('locale'))) {
        Session::put('locale', $locale);
        if(Auth::check()){
            Auth::user()->locale = $locale;
            Auth::user()->save();
        }
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('locale');

$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/dashboard', 'DashboardController@index')->name('dashboard')->middleware('auth:web');

//Facility Images
Route::get('facilities/{facility}/images', 'FacilityImageController@index')->name('facilities.images.index')->middleware('auth:web');
Route::get('facilities/{facility}/images/create', 'FacilityImageController@create')->name('facilities.images.create')->middleware('auth:web');
Route::post('facilities/{facility}/images', 'FacilityImageController@store')->name('facilities.images.store')->middleware('auth:web');
Route::get('facilities/{facility}/images/edit', 'FacilityImageController@edit')->name('facilities.images.edit')->middleware('auth:web');
Route::delete('facilities/{facility}/images', 'FacilityImageController@destroy')->name('facilities.images.destroy')->middleware('auth:web');

//Venues Images
Route::get('venues/{venue}/images', 'VenueImageController@index')->name('venues.images.index')->middleware('auth:web');
Route::get('venues/{venue}/images/create', 'VenueImageController@create')->name('venues.images.create')->middleware('auth:web');
Route::post('venues/{venue}/images', 'VenueImageController@store')->name('venues.images.store')->middleware('auth:web');
Route::get('venues/{venue}/images/edit', 'VenueImageController@edit')->name('venues.images.edit')->middleware('auth:web');
Route::delete('venues/{venue}/images', 'VenueImageController@destroy')->name('venues.images.destroy')->middleware('auth:web');

Route::resource('facilities', 'FacilityController')->middleware('auth:web');
Route::resource('venues', 'VenueController')->middleware('auth:web');
Route::resource('types', 'TypeController')->middleware('auth:web');
Route::resource('availabilities', 'VenueAvailabilityController')->middleware('auth:web');
Route::resource('admins', 'AdminController')->middleware('auth:web');
Route::resource('users', 'UserController')->middleware('auth:web');

//Venue
Route::post('venues/multi', 'VenueController@storeMulti')->name('venues.storeMulti')->middleware('auth:web');

//Reservations
Route::get('reservations/list', 'ReservationController@list')->name('reservations.list')->middleware('auth:web');
Route::get('reservations/calendar', 'ReservationController@calendar')->name('reservations.calendar')->middleware('auth:web');
Route::get('reservations', 'ReservationController@index')->name('reservations.index')->middleware('auth:web');
Route::get('reservations/create/{availability}', 'ReservationController@create')->name('reservations.create')->middleware('auth:web');
Route::post('reservationsCalendarStore', 'ReservationController@calendarStore')->name('reservations.calendarStore')->middleware('auth:web');
Route::post('reservationsCalendarUpdate', 'ReservationController@calendarUpdate')->name('reservations.calendarUpdate')->middleware('auth:web');
Route::post('reservationsCalendarDetailsUpdate', 'ReservationController@calendarDetailsUpdate')->name('reservations.calendarDetailsUpdate')->middleware('auth:web');
Route::post('reservationsCalendarDelete', 'ReservationController@calendarDelete')->name('reservations.calendarDelete')->middleware('auth:web');
Route::post('reservations', 'ReservationController@store')->name('reservations.store')->middleware('auth:web');
Route::get('reservations/{reservation}', 'ReservationController@show')->name('reservations.show')->middleware('auth:web');
Route::get('reservations/{reservation}/edit', 'ReservationController@edit')->name('reservations.edit')->middleware('auth:web');
Route::match(['patch', 'put'], 'reservations/{reservation}', 'ReservationController@update')->name('reservations.update')->middleware('auth:web');
Route::match(['patch', 'put'], 'reservations/{reservation}/noshow', 'ReservationController@noShow')->name('reservations.noShow')->middleware('auth:web');
Route::delete('reservations/{reservation}', 'ReservationController@destroy')->name('reservations.destroy')->middleware('auth:web');

//Recursive
Route::get('recursive', 'RecursiveController@index')->name('recursive.index')->middleware('auth:web');
Route::get('recursive/{recursive}', 'RecursiveController@show')->name('recursive.show')->middleware('auth:web');
Route::get('recursive/create/{availability}', 'RecursiveController@create')->name('recursive.create')->middleware('auth:web');
Route::post('recursive', 'RecursiveController@store')->name('recursive.store')->middleware('auth:web');
Route::delete('recursive/{recursive}', 'RecursiveController@destroy')->name('recursive.destroy')->middleware('auth:web');

//Profile
Route::get('/profile/{user}/edit', 'ProfileController@edit')->name('profile.edit')->middleware('auth:web');
Route::patch('/profile/{user}', 'ProfileController@update')->name('profile.update')->middleware('auth:web');

//Availabilities
Route::get('/venues/{venue}/edit/availabilities', 'VenueController@editAvailabilities')->name('venues.edit.availabilities')->middleware('auth:web');
Route::post('/venues/{venue}/availabilities', 'VenueController@updateAvailabilities')->name('venues.update.availabilities')->middleware('auth:web');

//Marker
Route::post('/markers', 'MarkersController@store')->name('markers.store')->middleware('auth:web');

//SMS Logs
Route::get('/sms', 'SmsLogController@index')->name('sms.index')->middleware('auth:web');
Route::get('/sms/{sms}', 'SmsLogController@show')->name('sms.show')->middleware('auth:web');