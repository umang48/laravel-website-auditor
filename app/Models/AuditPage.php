<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditPage extends Model
{
    use HasFactory;
    protected $fillable = ['audit_id', 'url', 'status_code', 'response_time', 'page_size', 'title', 'meta_description', 'canonical'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function issues()
    {
        return $this->hasMany(AuditIssue::class, 'page_id');
    }
}
