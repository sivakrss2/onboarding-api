<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCandidateTableFieldToNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->boolean('candidate_accomodation')->default(0)->change();
            $table->boolean('assigned_consultant_work')->default(0)->change();
            $table->integer('consultant_lead_id')->default(0)->change();
            $table->integer('technical_lead_id')->default(0)->change();
            $table->boolean('is_tech_required')->default(0)->change();
            $table->boolean('source_of_hire')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            //
        });
    }
}
