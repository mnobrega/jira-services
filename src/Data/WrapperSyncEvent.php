<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class WrapperSyncEvent extends Model
{
    protected $table = 'jira_wrapper_sync_events';
    protected $fillable = ['from_datetime','to_datetime'];
}
