<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Answer;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'text',
        'category_id',
        'type',
        'difficulty',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
