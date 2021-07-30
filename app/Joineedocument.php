<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Joineedocument extends Model
{
    protected $table = 'joinee_documents';
    protected $primary_key = 'id';

    protected $fillable = ['type', 'guid'];
}
