<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    /**
     * Auth Routes
     */
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('register', ['as' => 'api.auth.register', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@register']);
        $api->post('login', ['as' => 'api.auth.login', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@login']);
        $api->post('refresh', ['as' => 'api.auth.refresh', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@refresh']);
        $api->post('logout', ['as' => 'api.auth.logout', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@logout'])->middleware('auth:api');

        $api->post('password/forget', ['as' => 'api.auth.password.forget', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@forgetPassword']);
        $api->post('password/reset', ['as' => 'api.auth.password.reset', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@resetPassword']);

        $api->post('phone/code/send', ['as' => 'api.auth.phone.code.send', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@phoneCodeSend']);
        $api->post('phone/code/verify', ['as' => 'api.auth.phone.code.verify', 'uses' => 'App\\Api\\V1\\Controllers\\AuthController@phoneCodeVerify']);
    });

    /**
     * User Routes
     */
    $api->group(['prefix' => 'users', 'middleware' => 'auth:api'], function(Router $api) {
        $api->get('/{uid}', ['as' => 'api.users.show', 'uses' => 'App\\Api\\V1\\Controllers\\UserController@show']);
        $api->patch('/{uid}', ['as' => 'api.users.update', 'uses' => 'App\\Api\\V1\\Controllers\\UserController@update']);
        $api->get('/{uid}/reservations', ['as' => 'api.users.reservations', 'uses' => 'App\\Api\\V1\\Controllers\\UserController@reservations']);
    });

    /**
     * Facility Routes
     */
    $api->group(['prefix' => 'facilities', 'middleware' => 'auth:api'], function(Router $api) {
        $api->get('', ['as' => 'api.facilities.index', 'uses' => 'App\\Api\\V1\\Controllers\\FacilityController@index']);
        $api->get('/{fid}', ['as' => 'api.facilities.show', 'uses' => 'App\\Api\\V1\\Controllers\\FacilityController@show']);
    });

    /**
     * Venue Routes
     */
    $api->group(['prefix' => 'venues', 'middleware' => 'auth:api'], function(Router $api) {
        $api->get('', ['as' => 'api.venues.index', 'uses' => 'App\\Api\\V1\\Controllers\\VenueController@index']);
        $api->get('/{vid}', ['as' => 'api.venues.show', 'uses' => 'App\\Api\\V1\\Controllers\\VenueController@show']);

        $api->get('/{vid}/availabilities', ['as' => 'api.venues.availabilities', 'uses' => 'App\\Api\\V1\\Controllers\\VenueAvailabilityController@index']);
        $api->get('/{vid}/availabilities/{vaid}', ['as' => 'api.venues.availability', 'uses' => 'App\\Api\\V1\\Controllers\\VenueAvailabilityController@show']);

        $api->post('/{vid}/rate', ['as' => 'api.venues.rate', 'uses' => 'App\\Api\\V1\\Controllers\\VenueController@rate']);
    });

    /**
     * Reservation Routes
     */
    $api->group(['prefix' => 'reservations', 'middleware' => 'auth:api'], function(Router $api) {
        $api->post('', ['as' => 'api.reservations.store', 'uses' => 'App\\Api\\V1\\Controllers\\ReservationController@store']);
        $api->get('/{resid}', ['as' => 'api.reservations.show', 'uses' => 'App\\Api\\V1\\Controllers\\ReservationController@show']);
    });

    /**
     * Utility Routes
     */
    $api->group(['middleware' => 'auth:api'], function(Router $api) {
        $api->get('cities', ['as' => 'api.cities.index', 'uses' => 'App\\Api\\V1\\Controllers\\CityController@index']);
        $api->get('regions', ['as' => 'api.regions.index', 'uses' => 'App\\Api\\V1\\Controllers\\RegionController@index']);
        $api->get('types/venue', ['as' => 'api.venues.types', 'uses' => 'App\\Api\\V1\\Controllers\\VenueController@types']);
    });

    /**
     * App Routes
     */
    $api->group(['prefix' => 'app'], function(Router $api) {
        $api->get('/options', ['as' => 'api.app.options', 'uses' => 'App\\Api\\V1\\Controllers\\AppController@options']);
    });
});
