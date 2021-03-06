<?php

namespace App\Data;

use Illuminate\Database\Eloquent\SoftDeletes;
use Lucid\Foundation\Model;

class Issue extends Model
{
    use SoftDeletes;

    protected $table = 'jira_wrapper_issues';
    protected $fillable = ['issue_key','project_key','priority','ranking','type','status','summary','created','updated',
        'fix_version_id', 'epic_link','epic_name','epic_color','assignee','remaining_estimate','original_estimate'];
    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::deleting(function($issues) {
            /** @var \App\Data\Issue $issues */
            foreach ($issues->links()->get() as $link) {
                $link->delete();
            }
            foreach ($issues->histories()->get() as $history) {
                $history->delete();
            }
        });
    }

    public function sprints()
    {
        return $this->belongsToMany('App\Data\Sprint','jira_wrapper_sprints_issues',
            'issue_id','sprint_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany('App\Data\IssueHistory','issue_id', 'id');
    }

    public function links()
    {
        return $this->hasMany('App\Data\IssueLink','issue_id', 'id');
    }
}