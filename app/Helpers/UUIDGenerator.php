<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

trait UUIDGenerator
{
    protected static function bootUUIDGenerator()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }
}
