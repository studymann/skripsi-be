<?php

namespace App\Models;

use App\Helpers\UUIDGenerator;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, UUIDGenerator, SoftDeletes;

    protected $table = 'galleries'; // Specify the table name
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'tittle', 'description', 'image'
    ];


}
