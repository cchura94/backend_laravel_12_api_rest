<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->group(function(){

    // Rutas (subida de imagenes)
    Route::post("producto/{prod}/actualiza-imagen", [ProductoController::class, "funActualizaImagen"]);
    
    // CRUDs SQL
    Route::apiResource("categoria", CategoriaController::class);
    // CRUD Query Builder
    Route::apiResource("user", UserController::class);
    // CRUD Eloquent ORM
    Route::apiResource("producto", ProductoController::class);
    // CRUD DE Sucursal
    Route::apiResource("sucursal", SucursalController::class);
    
});

Route::get("/no-autorizado", function(){
    return response()->json(["mensaje" => "No estas autorizado para ver este sistema"]);
})->name("login");

