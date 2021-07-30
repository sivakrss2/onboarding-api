<?php

namespace App\Candidate;

use Illuminate\Database\Eloquent\Model;

class CandidateTechinicalTaskDetail extends Model
{
	protected $table = 'techinical_task_details';
    protected $primaryKey = 'id';
    protected $fillable = ['techinical_task_id','task_detail','task_start_date','task_end_date','task_status','created_by'];
		
}

?>