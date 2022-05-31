<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Email;

class AuthController extends Controller
{
    
    //login
    public function login(Request $request){
        //validar 
        $request->validate([
            "email"=>"required|email|string",
            "password"=>"required|string"
        ]);
        //verificar la credencial
        $credenciales=request(["email","password"]);
        if(!Auth::attempt($credenciales)){
            return response()->json([
                "Mensaje"=>"Acceso no autorizado"
            ],401);
        }
        //generar token
        $usuario=$request->user();
        $tokenResult= $usuario->createToken("token_login");
        $token=$tokenResult->plainTextToken;
        //responder token
        return response()->json([
            "Token_de_acceso"=>$token,
            "token_type"=>"Bearer"
        ],200); 
    }

    //registro
    public function registro(Request $request){
            //validad
            $request->validate([
                "name"=>"required|string",
                "email"=>"required|email|string|unique:users",
                "password"=>"required|string",
                "c_password"=>"required|same:password"
            ]);
            //guardar
                $usuario=new User;
                $usuario->name=$request->name;
                $usuario->email=$request->email;
                $usuario->password=bcrypt($request->password);
                $usuario->save();
            //opcional token
            $tokenResult=$usuario->createToken("mi_token");
            $token=$tokenResult->plainTextToken;
            //responder
            return response()->json([
                "mensaje"=>"usuario guardado",
                "token_de_acceso"=>$token
            ],201);
    }
    //perfil
    public function perfil(Request $request){
        //validad
        //guardar
        //opciones
        //return $request->user();
        $user=auth()->user();
        return response()->json($user, 200);
    }
    //logout
    public function logout(Request $request){
        $request->user()->delete();
        return response()->json([
            "Mensaje"=>"se eliminaron los token"
        ]);
    }
}
