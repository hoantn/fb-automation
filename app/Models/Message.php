<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'page_id','customer_id','direction','mid','text','attachments','status','sent_at','raw'
    ];
    protected $casts = ['attachments'=>'array','raw'=>'array','sent_at'=>'datetime'];

    public function page(){ return $this->belongsTo(Page::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
}
