<?php

namespace App\Candidate;

use Illuminate\Database\Eloquent\Model;

class CandidateJoineeDocument extends Model
{
	protected $table = 'candidate_joinee_documents';
    protected $primaryKey = 'id';
    protected $fillable = ['candidate_id','open_time','close_time','contract','joining_commitement','salary_break_up','joining_bonus','back_up_lead','contract_comment','joining_commitement_comment','salary_break_up_comment','joining_bonus_comment','back_up_lead_comment','created_by'];
		
}
?>