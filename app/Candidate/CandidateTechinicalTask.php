<?php

namespace App\Candidate;

use Illuminate\Database\Eloquent\Model;

class CandidateTechinicalTask extends Model
{
	protected $table = 'techinical_task';
    protected $primaryKey = 'id';
    protected $fillable = ['candidate_id','task_id','client_name','task_assigned','created_by','updated_by'];
        
    public function candidate_techinical_taskDetail() {
		return $this->hasMany('App\Candidate\CandidateTechinicalTaskDetail','techinical_task_id');
	}
}

?>