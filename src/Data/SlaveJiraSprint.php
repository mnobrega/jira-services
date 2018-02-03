<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraSprint extends Model
{
    protected $table = 'jira_sync_slave_jira_sprints';
    protected $fillable = ['master_sprint_id','slave_sprint_id'];
}
