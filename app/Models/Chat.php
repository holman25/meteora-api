<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasUuids;
    use HasFactory;

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


