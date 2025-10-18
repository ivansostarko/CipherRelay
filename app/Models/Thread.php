<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $fillable = ['passcode_hash','status','key_cipher','key_header'];
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at','asc');
    }
    public function notes(): HasMany
    {
        return $this->hasMany(MessageNote::class)->orderBy('created_at','asc');
    }
}
