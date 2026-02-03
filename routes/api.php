<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para autenticación

Route::prefix("/v1/auth")->group(function(){

    Route::post("/register", [AuthController::class, "funRegister"]);
    Route::post("/login", [AuthController::class, "funLogin"]);
    
    Route::middleware("auth:sanctum")->group(function(){

        Route::get("/profile", [AuthController::class, "funProfile"]);
        Route::post("/logout", [AuthController::class, "funLogout"]);

    });

});

Route::get("/no-autorizado", function(){
    return response()->json(["mensaje" => "No estas autorizado para ver este sistema"]);
})->name("login");

