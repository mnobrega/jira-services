<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueHistory extends Model
{
    use SoftDeletes;

    protected $table = 'jira_wrapper_issues_histories';
    protected $fillable = ['jira_id','created','field','field_type','from_string','to_string','author_name'];

    public function issue()
    {
        return $this->belongsTo('App\Data\Issue','issue_id');
    }


}