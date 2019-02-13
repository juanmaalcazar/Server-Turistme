<?php

namespace App\Http\Controllers;
use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use \Firebase\JWT\JWT;

class RegisterController extends Controller
{
    public function register (Request $request)
    {
                
        if($this->checkUserExist($request->email))
        {
            
            return $this->error(415,'El usuario ya existe');
        }

     if (!isset($_POST['name']) or !isset($_POST['email']) or !isset($_POST['password']))
     {
        return $this->error(401, 'No puedes dejar campos vacíos');
     }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $users = Users::where('email', $email)->get();

    foreach ($users as $user) 
    {
        if($user->email == $email)
        {
            return $this->error(400, 'El email ya existe, por favor utiliza otro distinto');
        }       

    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){

        return $this->error(400, 'Por favor introduce un email valido');

    }

    if (strlen($password) < 8){

        return $this->error(400, 'El password debe tener al menos 8 caracteres');
    }

    if (!empty($request->name) && !empty($request->email) && !empty($request->password))
        {
            try
            {    
        
                $users = new Users();
                $users->name = $request->name;
                $users->password = encrypt($request->password);
                $users->email = $request->email;
                $users->rol_id = 2;

                $users->save();
                              
            }
            catch(Exception $exception)
            {
                return $this->error(2, $exception->getMessage());
            }

            return $this->success('Usuario registrado');
        }   
        else
        {
            return $this->error(401,'No puede haber campos vacios');
        }    
    }
    
    public function checkUserExist($email)
    {
        $usersData = Users::where('email',$_POST['email'])->first();
        if(!is_null($usersData))
        {
            return true;
        }
        return false;
    }
}
