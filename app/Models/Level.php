<?php

namespace App\Models;

use App\Helpers\UUIDGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory, UUIDGenerator, SoftDeletes;

    protected $table = 'levels'; // Specify the table name
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'package_id', 'name'
    ];

    public function classes()
    {
        // return $this->belongsTo(Gallery::class);
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
}
