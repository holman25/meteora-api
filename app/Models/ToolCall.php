<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ToolCall extends Model {
    use HasUuids;
    protected $fillable = ['message_id','tool','request','response','status','latency_ms'];
    protected $casts = ['request'=>'array','response'=>'array'];
    public $incrementing = false;
    protected $keyType = 'string';
    public function message() { return $this->belongsTo(Message::class); }
}

