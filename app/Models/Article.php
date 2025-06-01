<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author',  
        'category_id',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
   protected static function booted() :void
   {
    static::creating(function($article){
        if (Auth::check()){
            $article->author = Auth::user()->name;
        }
    });
   }
}

