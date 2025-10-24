<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Chat extends Model
{
    use HasUuids;

    protected $fillable = [];
    public $incrementing = false;
    protected $keyType = 'string';
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}


