<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'value',
        'title',
        'description',
        'domain'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relationships Many to Many
    public function users(){
        return $this->belongsToMany(User::class, 'config_users', 'idconfig', 'iduser')->withPivot('value', 'description')->withTimestamps();
    }
}
