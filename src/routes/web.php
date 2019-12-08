<?php

Route::group(['namespace'=>'Aramics\Translator\Http\Controllers','prefix'=>'translator'],function(){
	Route::get('/',['uses'=>'TranslatorController@index']);
    Route::get('download',['uses'=>'TranslatorController@export']);
    Route::post('/',['uses'=>'TranslatorController@import']);
});

?>
