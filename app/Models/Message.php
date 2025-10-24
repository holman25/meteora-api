<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Message extends Model {
    use HasUuids;
    protected $fillable = ['chat_id','role','content','model','status','error_code','metadata'];
    protected $casts = ['metadata' => 'array'];
    public $incrementing = false;
    protected $keyType = 'string';
    public function chat() { return $this->belongsTo(Chat::class); }
    public function toolCalls() { return $this->hasMany(ToolCall::class); }
}

