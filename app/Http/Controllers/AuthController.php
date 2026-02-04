<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function funRegister(Request $request){
        // Registrar un nuevo Usuario
        // validar
        $request->validate([
            "name" => "required|string|min:3|max:30",
            "email" => "required|email",
            "password" => "required",
            "password2" => "required|same:password"
        ]);

        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->password); // bcrypt($request->password);
        $usuario->save();

        // return view("admin.registro");
        
        return response()->json(["mensaje" => "Usuario Registrado"], 201);
    }

    public function funLogin(Request $request){
        // Autenticación

        // validar
        $credenciales = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        
        if(!Auth::attempt($credenciales)){
            return response()->json(["mensaje" => "Credenciales Incorrectas"]);
        }

        // generar TOKEN
        $token = $request->user()->createToken("TokenAuth")->plainTextToken;

        return response()->json(["access_token" => $token, "usuario" => $request->user()]);
    }

    public function funProfile(Request $request){
        // 
        return response()->json($request->user());
    }

    public function funLogout(Request $request){
        $usuario = $request->user();
        $usuario->tokens()->delete();
        return response()->json(["mensaje" => "salio"], 200);
    }
}
