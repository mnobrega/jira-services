<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SyncEvent extends Model
{
    protected $table = 'jira_sync_sync_events';
    protected $fillable = ['from_datetime','to_datetime'];
}
