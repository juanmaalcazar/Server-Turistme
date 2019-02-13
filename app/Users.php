<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{

	protected $fillable = ['name', 'password', 'email', 'rol_id'];

	protected $table = 'users';

    public function rols()
    {
    	return $this -> belongsTo ('App\Rols');

    }

    public function places()
    {
    	return $this -> hasMany ('App\Places');
    }


}
