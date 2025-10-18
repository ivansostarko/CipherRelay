<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageNote extends Model
{
    protected $fillable = ['thread_id','note'];
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
