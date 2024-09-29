<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewedModel extends Model
{
    use HasFactory;

    protected $table = 'viewed';

    protected $fillable = [
        'project_id',
        'user_id',
        'file_name',
    ];
}
