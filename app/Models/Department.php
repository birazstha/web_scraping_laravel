<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    public $guarded = ['id'];
    public $table = 'departments';

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }
}
