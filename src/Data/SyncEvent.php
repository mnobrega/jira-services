<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class SyncEvent extends Model
{
    protected $table = 'sync_events';
    protected $fillable = ['from_datetime','to_datetime','tuples_created','tuples_updated','tuples_deleted'];
}
