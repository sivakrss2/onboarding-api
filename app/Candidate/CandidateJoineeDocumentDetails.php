<?php

namespace App\Candidate;

use Illuminate\Database\Eloquent\Model;

class CandidateJoineeDocumentDetails extends Model
{
	protected $table = 'candidate_joinee_documents_details';
    protected $primaryKey = 'id';
    protected $fillable = ['candidate_joinee_document_id', 'file_mime', 'file_data', 'file_name', 'type', 'path', 'created_by'];
		
}
?>