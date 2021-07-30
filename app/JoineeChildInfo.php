<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoineeChildInfo extends Model
{
    protected $table = 'joinee_child_info';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'joinee_id', 'child_name', 'child_gender', 'child_dob'
    ];

}
