<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Users;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email)){

            return $this->error (401, 'Por favor introduce un email');

        }

        if (empty($password)){

            return $this->error (401, 'Por favor introduce el password');

        }

        $users = Users::where('email', $email)->get();


        if ($users->isEmpty()) { 

            return $this->error(400, "Ese usuario no existe, por favor introduce un email correcto");

        }
      
        $userDecrypt = Users::where('email', $email)->first();


        $passwordHold = $userDecrypt->password;

        $decryptedPassword = decrypt($passwordHold);
        $key = $this->key;

        if (self::checkLogin($email, $password))
        {
            
            $userSave = Users::where('email', $email)->first();


            $usersData = array
            (
                'id' => $userSave->id,
                'name' => $userSave->name,
                'email' => $email,
                'password' => $password,
                'rol_id' => $userSave->rol_id
                
            );

            
            $token = JWT::encode($usersData, $key);

            return $this->success('Usuario Logeado', $token);

        }
        else
        {
            return response("Los datos no son correctos", 403)->header('Access-Control-Allow-Origin', '*');
        }
        

    }
}