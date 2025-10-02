<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoModel extends Model
{
    protected $connection = 'mongodb';
    
    // Rest of your model code
}
