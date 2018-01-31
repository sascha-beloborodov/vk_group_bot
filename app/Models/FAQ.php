<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FAQ
 * @package App\Models
 * @version January 31, 2018, 1:33 pm UTC
 *
 * @property string question
 * @property string answer
 * @property string keywords
 * @property integer order
 * @property tinyinteger is_active
 * @property timestamp created_at
 * @property timestamp updated_at
 */
class FAQ extends Model
{
    use SoftDeletes;

    public $table = 'f_a_q_s';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'question',
        'answer',
        'keywords',
        'order',
        'is_active',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'string',
        'answer' => 'string',
        'keywords' => 'string',
        'order' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'question' => 'required,min:2',
        'answer' => 'required,min:2',
        'order' => 'integer',
        'is_active' => 'integer'
    ];

    
}
