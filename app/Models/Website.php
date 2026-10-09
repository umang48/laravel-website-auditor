<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;
use App\Models\Audit;

class Website extends Model
{
    use HasFactory; // Add this
    
    protected $fillable = ['user_id', 'name', 'url', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
