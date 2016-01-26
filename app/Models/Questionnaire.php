<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'questionnaires';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'interval'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get all of the questions.
     */
    public function blocks()
    {
        return $this->hasMany('App\Models\Block', 'questionnaire_id');
    }

    /**
     * Get the user who owns the questionnaire.
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'owner_id');
    }

    /**
     * Get the user who created the questionnaire.
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }

    /**
     * Get the videos attached to this questionnaire.
     */
    public function videos()
    {
        return $this->belongsToMany('App\Models\Video','questionnaire_video', 'questionnaire_id', 'video_id')->withTimestamps();
    }
}
