<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'thread_id','direction','subject_cipher','subject_header','body_cipher','body_header',
        'file_path','file_original','file_size'
    ];
    protected $casts = [
        'file_size' => 'integer',
    ];
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
