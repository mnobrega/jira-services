<?php

namespace App\Data;

use Lucid\Foundation\Model;

class Issue extends Model
{
    protected $table = 'jira_wrapper_issues';
    protected $fillable = ['key','project_key','priority','ranking','type','status','summary','created','updated',
        'fix_version', 'epic_link','assignee','remaining_estimate','original_estimate'];
}