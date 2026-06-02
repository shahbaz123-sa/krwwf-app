<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    // List all conversations for the authenticated user
    public function conversations(Request $request)
    {
        $user = Auth::user();
        $conversations = ChatbotConversation::where('user_id', $user->id)->orderByDesc('updated_at')->get();
        return response()->json($conversations);
    }

    // Create a new conversation
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        $conversation = ChatbotConversation::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'status' => 'active',
        ]);
        return response()->json($conversation);
    }

    // Get messages for a conversation
    public function messages($id)
    {
        $user = Auth::user();
        $conversation = ChatbotConversation::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $messages = $conversation->messages()->orderBy('created_at')->get();
        return response()->json($messages);
    }

    // Send a message in a conversation
    public function sendMessage(Request $request, $id)
    {
        $user = Auth::user();
        $conversation = ChatbotConversation::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $message = ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'sender' => 'user',
            'message' => $request->input('message'),
        ]);
        // Optionally, trigger bot response here
        return response()->json($message);
    }
}

