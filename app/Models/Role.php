<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Role extends Model
{

    public $table = 'roles';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
