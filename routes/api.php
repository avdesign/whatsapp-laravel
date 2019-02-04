<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {

    Route::post('login', 'AuthController@login')->name('login');
    Route::post('login_vendor', 'AuthController@loginFirebase')->name('login_vendor');
    Route::post('refresh', 'AuthController@refresh')->name('refresh');

    Route::post('customers/phone_numbers', 'CustomerController@requestPhoneNumberUpdate');
    Route::patch('customers/phone_numbers/{token}', 'CustomerController@updatePhoneNumber');
    Route::resource('customers', 'CustomerController', ['only' => ['store']]);

    /** Vendedores e Usuários Autenticados */
    Route::group(['middleware' => ['auth:api', 'jwt.refresh']], function(){

        Route::name('logout')->post('logout', 'AuthController@logout');
        Route::patch('profile', 'UserProfileController@update');
        Route::resource('chat_groups.messages', 'ChatMessageFbController',['only' => ['store']]);

        // Convites para Users
        Route::post('chat_invitations/{invitation_slug}', 'ChatInvitationUserController@store');

        /** Routes Api/Open */
        Route::group(['prefix' => 'open', 'namespace' => 'Open'], function (){
            Route::resource('products', 'ProductController', ['only' => ['index', 'show']]);
            Route::get('categories', 'CategoryController@index');
        });


        //IS SELLER
        Route::group(['middleware' => ['can:is_seller']], function () {
            /** Permissão Vendedores */
            Route::name('me')->post('me', 'AuthController@me');

            Route::resource('users', 'UserController', ['except' => ['create', 'edit']]);

            Route::patch('products/{product}/restore', 'ProductController@restore');
            Route::resource('products', 'ProductController', ['except' => ['create', 'edit']]);
            Route::resource('categories', 'CategoryController', ['except' => ['create', 'edit']]);
            Route::resource('products.categories', 'ProductCategoryController', ['only' => ['index', 'store', 'destroy']]);
            Route::resource('products.photos', 'ProductPhotoController', ['except' => ['create', 'edit']]);
            Route::resource('inputs', 'ProductInputController', ['only' => ['index', 'store', 'show']]);
            Route::resource('outputs', 'ProductOutputController', ['only' => ['index', 'store', 'show']]);

            Route::resource('chat_groups', 'ChatGroupController', ['except' => ['create', 'edit']]);
            Route::resource('chat_groups.users', 'ChatGroupUserController', ['only' => ['index', 'store', 'destroy']]);

            // Convites "Invitations"
            Route::resource('chat_groups.link_invitations', 'ChatGroupInvitationController', ['except' => ['create', 'edit']]);
            Route::resource('chat_groups.invitations', 'ChatInvitationUserController', ['only' => ['index', 'show', 'update']]);

        });



    });
   
});





