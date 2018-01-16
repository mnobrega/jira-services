<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $table = 'issues';
    protected $fillable = ['key','project_key','rank','priority','type','status','summary',
        'created','updated','fix_version','epic_link','assignee','remaining_estimate','original_estimate'];
}