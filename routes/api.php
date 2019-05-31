<?php

Route::group(
    [
        'middleware' => ['auth:api'],
        'prefix' => 'api/data',
        'namespace' => 'JDD\Api\Http\Controllers',
    ],
    function () {
        Route::get(
            '/{module}/{model1?}/{id1?}/{model2?}/{id2?}/{model3?}/{id3?}/{model4?}/{id4?}/{model5?}/{id5?}',
            ['uses' => 'ApiController@index']
        );

        Route::post(
            '/{module}/{model1?}/{id1?}/{model2?}/{id2?}/{model3?}/{id3?}/{model4?}/{id4?}/{model5?}/{id5?}',
            ['uses' => 'ApiController@store']
        );

        Route::patch(
            '/{module}/{model1?}/{id1?}/{model2?}/{id2?}/{model3?}/{id3?}/{model4?}/{id4?}/{model5?}/{id5?}',
            ['uses' => 'ApiController@update']
        );

        Route::put(
            '/{module}/{model1?}/{id1?}/{model2?}/{id2?}/{model3?}/{id3?}/{model4?}/{id4?}/{model5?}/{id5?}',
            ['uses' => 'ApiController@update']
        );

        Route::delete(
            '/{module}/{model1?}/{id1?}/{model2?}/{id2?}/{model3?}/{id3?}/{model4?}/{id4?}/{model5?}/{id5?}',
            ['uses' => 'ApiController@delete']
        );
    }
);
