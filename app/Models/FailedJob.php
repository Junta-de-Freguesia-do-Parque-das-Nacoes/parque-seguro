<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    protected $fillable = ['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Guid::uuid4();
            }
        });
    }
}
