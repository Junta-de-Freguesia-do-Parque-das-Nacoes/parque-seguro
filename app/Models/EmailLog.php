<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    // Especifique os campos que você deseja preencher
    protected $fillable = [
        'email',
        'subject',
        'body',
        'status',
        'sent_at',
    ];
}
