<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Audit extends Model
{
    use HasFactory;
    
    protected $fillable = ['website_id', 'status', 'score', 'started_at', 'completed_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function pages()
    {
        return $this->hasMany(AuditPage::class);
    }

    public function issues()
    {
        return $this->hasMany(AuditIssue::class);
    }
}
