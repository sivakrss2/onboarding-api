<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoineePersonalReference extends Model
{
    protected $table = 'joinee_personal_reference';
    protected $primary_key = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'designation', 'company_name', 'phone_no', 'email', 'relation', 'created_at', 'updated_at','guid'
    ];

    public function joinee()
    {
        return $this->belongsTo('App\JoineePersonal');
    }
}
