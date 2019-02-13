<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = getallheaders();
        $userParams = JWT::decode($header['Authorization'], $this->key, array('HS256'));
        if ($userParams->rol_id == 1) {
            $allUsers = Users::all();
            return $this->success('Users', $allUsers);
        }
        else
        {
            return response()->json([
                'message' => 403, 'No tienes suficientes permisos'
            ]);
        }
        
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    public function updateUser()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = $_POST['idUser'];
        $newName = $_POST['newName'];
        $newEmail = $_POST['newEmail'];
        $newPassword = $_POST['newPassword'];

        $id = $id_user;
        $user = Users::find($id);
        $userObj = Users::where('email', $userData->email)->first();
        $rol = $userObj->rol_id;

        if ($rol == 1)
        {

            if (is_null($user)) {
                return $this->error(400, 'El usuario no existe');
            }

            if (!empty($_POST['newName']) ) {
                $user->name = $newName;
            }
            if (!empty($_POST['newEmail']) ) {
                $user->email = $newEmail;
            }
            if (!empty($_POST['newPassword']) ) {
                $user->password =encrypt($newPassword);
            }             
                $user->save();
                return $this->success(200, 'Usuario Actualizado');
                
        }else{
            return $this->error(401, 'No tienes permisos para editar');
        }



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
   
    public function deleteUser()
    {
        
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = Users::where('email', $userData->email)->first()->id;
        $id_users = $_POST['idUser'];
        $id = $id_users;

        $user = Users::find($id);

        $rolUser = Users::where('email', $userData->email)->first();
        

        if ($rolUser->rol_id == 1){

            $user_name = Users::where('id', $id_users)->first()->name;
                Users::destroy($id);

            return $this->success('Acabas de borrar a', $user_name);

        }else{
            return $this->error(403, 'No tienes permisos');
        }
  

        if (is_null($user)) 
        {
            return $this->error(400, 'El lugar no existe');
        }
            // }else{

            //     $user_name = Users::where('id', $id_users)->first()->name;
            //     Users::destroy($id);

            // return $this->success('Carlos he borrado el usuario', $user_name);
            // }
    }






    public function destroy(Users $users)
    { 
        // $headers = getallheaders();
        // $token = $headers['Authorization'];
        // $key = $this->key;
        // $userData = JWT::decode($token, $key, array('HS256'));
        // $id_user = Users::where('email', $userData->email)->first()->id;
        // $id_users = $_POST['idUser'];
        // $id = $id_users;
        // $user = Users::find($id);
        // if (is_null($user)) {
        //     return $this->error(400, 'El lugar no existe');
        // }else{
        //     $user_name = Users::where('id', $id_users)->first()->name;
        //     Users::destroy($id);

        // return $this->success('Lugar Borrado', $user_name);
        // }
        
    }
}
