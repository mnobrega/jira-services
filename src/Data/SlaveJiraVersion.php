<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraVersion extends Model
{
    protected $table = 'jira_sync_slave_jira_versions';
    protected $fillable = ['master_version_id','slave_version_id'];
}
