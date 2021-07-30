<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
	protected $connection = 'sqlsrv';
	protected $table = 'CGVak_EmployeeMaster';
}
