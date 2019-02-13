<?php

namespace App\Http\Controllers;

use App\Places;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Users;



class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $headers = getallheaders();
        $token = $headers['Authorization'];

        if ($token != null){

            $parameters = JWT::decode($headers['Authorization'], $this->key, array('HS256'));

            $id = Users::where('email', $parameters->email)->first()->id;


            $places = Places::where('users_id', $id)->get();

            foreach ($places as $key => $place) 
            {    
                return response()->json(['MySites'=>$places]);    
            }
        }
        
        else 
        {
            return response()->json([403,'No tienes suficientes permisos']);
        }
        


        

        /*
        $headers = getallheaders();
        $token = $headers['Authorization'];

        $key = $this->key;

        $userData = JWT::decode($token, $key, array('HS256'));

        $id = Users::where('email', $userData->email)->first()->id;

        $places = Places::where('users_id', $id)->get();

        if ($places->isEmpty()) { 
            return $this->error(400, "No hay lugares guardados");
        }
        $placeNames = [];
        $placeIDs = [];


        foreach ($places as $place) {
            array_push($placeNames, $place->name);
            array_push($placeIDs, $place->id);
            } 

        return response()->json ([
                'places' => $placeNames,
                'ids' => $placeIDs
            ]);*/
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
        $headers = getallheaders();
        $token = $headers['Authorization'];

        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));

        $placeName = $_POST['placeName'];
        $xCoord = $_POST['xCoordinate'];
        $yCoord = $_POST['yCoordinate'];
        $description = $_POST['description'];
        
        $start_Date = strtotime($_POST['startDate']);
        $newStartFormat = date('y-m-d', $start_Date);
        

        $end_Date = strtotime($_POST['endDate']);
        $newEndFormat = date('y-m-d', $end_Date);    
        
        
        $id = Users::where('email', $userData->email)->first()->id;

        $places = Places::where('users_id', $id)->get();

        
        foreach ($places as $place) 

        {
            if ($place->name == $placeName) 
            {
                return $this->error(400, 'El nombre del lugar ya existe'); 
            }
        }

        if (!preg_match("/^[a-zA-Z ]*$/",$placeName)) {
            return $this->error(400, 'El nombre del lugar solo puede contener caracteres sin espacios en blanco'); 
        }

        if (empty($placeName)) {
            return $this->error(400, 'Por favor introduce un nombre para el lugar');
        } 

        if (empty($yCoord) || empty($xCoord)) {
            return $this->error(400, 'Tienes que introducir ambas coordenadas');
        } 

        if (empty($start_Date) || empty($end_Date)) {
            return $this->error(400, 'Tienes que introducir ambas fechas');
        } 


        if ($this->checkLogin($userData->email , $userData->password)) 
        { 
            

            $place = new Places();
            $place->name = $placeName;
            $place->coordinate_x = $xCoord;
            $place->coordinate_y = $yCoord;
            $place->description = $description;

            $start_Date = strtotime($_POST['startDate']);
            $newStartFormat = date('y-m-d', $start_Date);
            $place->start_Date = $newStartFormat;

            $end_Date = strtotime($_POST['endDate']);
            $newEndFormat = date('y-m-d', $end_Date);
            $place->end_Date = $newEndFormat;       

            $place->users_id = $id;
            $place->save();

            return $this->success('Lugar creado', $request->placeName);

        }
        else
        {
            return $this->error(401, "No tienes permisos");
        }
     }
   
    /**
     * Display the specified resource.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    
    public function deletePlace()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = Users::where('email', $userData->email)->first()->id;
        $id_place = $_POST['idPlace'];
        $id = $id_place;
        $place = Places::find($id);

        if (is_null($place)) 
        {
            return $this->error(400, 'El lugar no existe');
        }

        $checkPlace = Places::where('users_id',$userData->id)->count();
        
        if (empty($checkPlace)) 
        {
           return $this->error(401, 'No tienes permisos de administrador');

        }else{
            
            $id_userPlace = Places::where('users_id', $userData->id)->first()->users_id;

            if($id_user != $id_userPlace)
            {
                return $this->error(401, 'No tienes suficientes permisos');
            }
        }

        

        if($id_user == $id_userPlace){

            $place_name = Places::where('id', $id_place)->first()->name;
            Places::destroy($id);

            return $this->success('Lugar Borrado', $place_name);
            
        }
    }

    public function updatePlace()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_place = $_POST['idPlace'];
        $newName = $_POST['newName'];
        $newX = $_POST['newXCoord'];
        $newY = $_POST['newYCoord'];
        $newInitial = $_POST['newInitial'];
        $newEnd = $_POST['newEnd'];

        $id = $id_place;
        $place = Places::find($id);



        if (is_null($place)) {
            return $this->error(400, 'El lugar no existe');
        }

        if (!empty($_POST['newName']) ) {
            $place->name = $newName;
        }
        if (!empty($_POST['newXCoord']) ) {
            $place->coordinate_x = $newX;
        }
        if (!empty($_POST['newYCoord']) ) {
            $place->coordinate_y = $newY;
        }             
        if (!empty($_POST['newInitial']) ) {
            $place->start_date = $newInitial;
        }
        if (!empty($_POST['newEnd']) ) {
            $place->end_date = $newEnd;
        }           
            $place->save();
        return $this->success('Lugar Actualizado');
        

    }

    public function show(Place $place)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function edit(Place $place)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Place $place)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Place  $place
     * @return \Illuminate\Http\Response
     */
    public function destroy(Places $place)
    {

    }
}