<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    
    protected $table = 'places';
	protected $fillable = ['name', 'desciption', 'start_date', 'end_date', 'coordinate_x', 'coordinate_y', 'users_id'];



    public function users()
    {
    	return $this -> belongsTo ('App\Users');
    }
}
