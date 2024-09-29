<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionModel extends Model
{
    use HasFactory;

    protected $table = 'collection';

    protected $fillable = [
        'project_id',
        'manuscript',
        'poster',
        'video'
    ];
}
