<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoineePreviousCompany extends Model
{
    protected $table = 'joinee_previous_company';
    protected $primary_key = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hr_name', 'hr_designation', 'hr_phone_no', 'hr_email', 'ra_name', 'ra_designation',
        'ra_phone_no', 'ra_email', 'created_at', 'updated_at','guid'

    ];
}
