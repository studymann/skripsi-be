<?php

namespace App\Models;

use App\Helpers\UUIDGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory, UUIDGenerator, SoftDeletes;

    protected $table = 'classes'; // Specify the table name
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'name', 'package_id', 'level_id', 'semester_id', 'year'
    ];

    public function package()
    {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }

    public function level()
    {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }

    public function semester()
    {
        return $this->hasOne(Semester::class, 'id', 'semester_id');
    }

    public function student()
    {
        return $this->belongsToMany(User::class, 'class_users', 'class_id', 'student_id');
    }
}
