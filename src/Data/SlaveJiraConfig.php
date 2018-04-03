<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SlaveJiraConfig extends Model
{
    protected $table = 'jira_sync_slave_jira_config';
    protected $fillable = ['jira_board_name','jira_board_type'];
}
