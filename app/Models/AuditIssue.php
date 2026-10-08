<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditIssue extends Model
{
    protected $fillable = ['audit_id', 'page_id', 'type', 'severity', 'message', 'recommendation'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function page()
    {
        return $this->belongsTo(AuditPage::class, 'page_id');
    }
}
