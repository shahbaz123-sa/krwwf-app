<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'title', 'status'
    ];

    public function messages()
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

