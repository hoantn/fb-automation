<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['page_id','customer_id','last_message_at'];
    protected $casts = ['last_message_at' => 'datetime'];

    public function page(){ return $this->belongsTo(Page::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function messages(){ return $this->hasMany(Message::class); }
}
