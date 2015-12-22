<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'blocks';

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
    protected $fillable = ['type', 'order', 'parent_id', 'data'];

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
     * Get the users attached to this group (user).
     */
    public function questionnaire()
    {
        return $this->belongsTo('App\Models\Questionnaire', 'questionnaire_id');
    }

    /**
     * Get the parent block.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Block', 'parent_id');
    }

    /**
     * Get the child blocks.
     */
    public function children()
    {
        return $this->hasMany('App\Models\Block', 'parent_id');
    }
}
