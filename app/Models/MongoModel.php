<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoModel extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    
    /**
     * The name of the collection associated with the model.
     *
     * @var string
     */
    protected $collection;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
}
