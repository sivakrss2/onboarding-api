<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoineePersonal extends Model
{
    protected $table = 'joinee_personal_info';
    protected $primaryKey = 'joinee_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'contact_number', 'alternate_number', 'father_name', 'father_contact_number', 'mother_name', 'mother_contact_number', 'marital_status', 'spouse_name',
        'spouse_contact_number', 'spouse_dob', 'present_address', 'permanent_address', 'date_of_birth', 'date_of_join', 'blood_group', 'email','guid','landmark','uan_no','is_link_disabled','created_at','updated_at'
    ];

    public function personalRefrences()
    {
        return $this->hasMany('App\JoineePersonalReference');
    }
}
