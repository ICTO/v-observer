<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'size', 'type', 'data'];

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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the creator of the video.
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\User','creator_id');
    }

    /**
     * Get the questionnaires attached to this video.
     */
    public function questionnaires()
    {
        return $this->belongsToMany('App\Models\Questionnaire','questionnaire_video', 'video_id', 'questionnaire_id')->withTimestamps();
    }

}
