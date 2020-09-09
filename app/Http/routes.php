<?php

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function () {
    Route::group(['as' => 'protected.', 'middleware' => 'api.auth'], function () {
        Route::get('info', ['as' => 'api.user.info', 'uses' => 'Api\UserController@info']);
        Route::resource('user', 'Api\UserController', ['only' => ['show', 'update', 'store']]);
        Route::post('comments/{id}/abuse',
            [
                'as'   => 'api.comments.abuse',
                'uses' => 'Api\CommentsController@abuse'
            ]);
        Route::post('post/{id}/comments', ['uses' => 'Api\CommentsController@create']);
        Route::post('post/{id}/favorite', ['uses' => 'Api\PostController@favorite']);
        Route::post('post/{id}/like', ['uses' => 'Api\PostController@like']);
        Route::get('favorites', ['uses' => 'Api\PostController@favorites']);
    });

    Route::group(['as' => 'public.', 'middleware' => 'api.token'], function () {
        Route::resource('category', 'Api\CategoryController', ['only' => ['index', 'show']]);
        Route::resource('post', 'Api\PostController', ['only' => ['index', 'show']]);
        Route::resource('post.comments', 'Api\CommentsController', ['only' => ['index']]);

        Route::post('auth/token', ['as' => 'api.auth.token', 'uses' => 'Api\AuthController@login']);
        Route::post('auth/email', ['as' => 'api.auth.email', 'uses' => 'Api\AuthController@loginByEmail']);
    });
});

Route::auth();

Route::get('/home', 'HomeController@index');
