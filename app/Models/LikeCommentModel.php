<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeCommentModel extends Model
{
    use HasFactory;

    protected $table = 'like_comment';

    protected $fillable = [
        'user_id',
        'project_id',
        'rating',
        'comment'
    ];
}
