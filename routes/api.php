<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use NasimTelecom\Simotel\Laravel\Facade\Simotel;

Route::get("simotel/smartApi",function(Request $request){

    try {
        $respond = Simotel::smartApi($request->all())->toArray();
        return response()->json($respond);
    } catch (\Exception $exception) {
        die("error: " . $exception->getMessage());
    }

});

Route::get("simotel/events",function(Request $request, $event){

        try {
           Simotel::eventApi()->dispatch($event,$request->all());
        } catch (\Exception $exception) {
            die("error: " . $exception->getMessage());
        }

});