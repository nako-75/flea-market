<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'description',
        'img_url',
        'condition',
        'brand_name',
    ];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function getConditionTextAttribute(){
    $conditions = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];
    return $conditions[$this->condition] ?? '未設定';
    }

    public function likedByUsers(){
        return $this->belongsToMany(User::class, 'likes');
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function purchase(){
        return $this->hasOne(Purchase::class);
    }
}


