<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/',array('uses' => 'HomeController@index', 'as' => 'home'));

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    // Search feed public routes
    Route::get('/search-feeds/schema/input',array('uses' => 'SearchFeedsController@getInputSchema', 'as' => 'search_schema_input'));
    Route::get('/search-feeds/schema/output',array('uses' => 'SearchFeedsController@getOutputSchema', 'as' => 'search_schema_output'));
});

/*
 * api routes
 *
 */

Route::group(['prefix' => 'api','middleware' => 'cors',  'namespace' => 'Api'], function() {

    Route::group(['prefix' => 'v1',  'namespace' => 'V1'], function() {

		//route search
		Route::get('search', ['uses' => 'SearchController@index','as' => 'search']);
		Route::post('search', ['uses' => 'SearchController@index','as' => 'search']);

		Route::get('search/viewed', ['uses' => 'SearchController@handle_search_view','as' => 'search_view_handler']);

		Route::get('whitelist_domain/check', ['uses' => 'WhitelistDomainController@check']);

		Route::get('publishers/add', ['uses' => 'PublishersController@add_publisher']);
		Route::get('publishers/get', ['uses' => 'PublishersController@get_all_publishers']);
		Route::get('publishers/click', ['uses' => 'PublishersController@handle_click', 'as'=>'search_item_click_handler']);

    });

});

Route::get('visionapi', function () {

    $visionapi=new \App\Helpers\VisionApi();
    $visionapi->init();
    //return Response::make(array("hello1","hello2"));

});

Route::get('currencyapi', function () {

    $currencyapi=new \App\Helpers\CurrencyLayerExchangeRateApi();
    $currencyapi->init();

});