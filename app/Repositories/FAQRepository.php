<?php

namespace App\Repositories;

use App\Models\FAQ;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FAQRepository
 * @package App\Repositories
 * @version January 31, 2018, 1:33 pm UTC
 *
 * @method FAQ findWithoutFail($id, $columns = ['*'])
 * @method FAQ find($id, $columns = ['*'])
 * @method FAQ first($columns = ['*'])
*/
class FAQRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question',
        'answer',
        'keywords',
        'order',
        'is_active',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FAQ::class;
    }
}
