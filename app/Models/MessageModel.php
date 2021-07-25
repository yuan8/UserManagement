<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    use HasFactory;
    protected $table='messages';

    protected $fillable=[
        'id','app_id','from_number','to_number','content_text','content_attach'
    ];
}
