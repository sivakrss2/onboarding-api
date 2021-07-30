<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDesignation extends Model
{
	protected $connection = 'sqlsrv';
	protected $table = 'CGVak_DesignationMaster';
}

