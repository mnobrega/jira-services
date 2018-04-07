<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraSprint extends Model
{
    protected $table = 'jira_sync_slave_jira_sprints';
    protected $fillable = ['master_sprint_jira_id','slave_sprint_jira_id'];

    public function sprint()
    {
        $this->hasOne('\App\Data\Sprint','jira_id','master_sprint_jira_id');
    }
}
