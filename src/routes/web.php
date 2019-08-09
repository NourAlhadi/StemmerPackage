<?php

Route::group(['namespace' => 'Nouralhadi\Stemmer\Http\Controllers'],function(){
    Route::get('stemmer','StemmerController@index')->name('stemmer');
    Route::post('stemmer/stem','StemmerController@stem')->name('stem');
});

