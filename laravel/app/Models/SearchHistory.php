<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'search_histories';

    protected $fillable = [
        'keyword',
        'count',
    ];
}
