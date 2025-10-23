<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['page_id','psid','name','avatar','last_interaction_at','meta'];
    protected $casts = ['meta' => 'array', 'last_interaction_at' => 'datetime'];

    public function page(){ return $this->belongsTo(Page::class); }
    public function conversation(){ return $this->hasOne(Conversation::class); }
    public function messages(){ return $this->hasMany(Message::class); }
}
