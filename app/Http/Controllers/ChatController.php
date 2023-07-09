<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $user;
    protected $chat;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user,Chat $chat)
    {
        $this->user = $user;
        $this->chat = $chat;
        $this->middleware('auth');
    }
    public function index()
    {
        $users = $this->user->all()->except(auth()->user()->id);
        return view('chat.index', ['users'=>$users]);
    }

    public function messenger()
    {
        $users = $this->user->all()->except(auth()->user()->id);
        return view('chat.messenger', ['users'=>$users]);
    }

        /**
     * Show the chat page.
     *
     * @return view
     */
    public function chat($id)
    {
        $fromId = Auth::user();
        $toId = $this->user->where('id',$id)->select('id','name')->first();
        $from = $fromId->id;
        $to = $toId->id;
        $chats = $this->chat->where(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $from)
                  ->where('to_id', '=', $to);
        })
        ->orWhere(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $to)
                  ->where('to_id', '=', $from);
        })->get();
        return view('chat.chat',compact('fromId','toId','chats'));
    }

    /**
     * Show the chat page.
     *
     * @return view
     */
    public function getChat(Request $request)
    {
        $fromId = Auth::user();
        $toId = $this->user->where('id',$request->id)->select('id','name')->first();
        $from = $fromId->id;
        $to = $toId->id;
        $chats = $this->chat->where(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $from)
                  ->where('to_id', '=', $to);
        })
        ->orWhere(function ($query) use ($from,$to) {
            $query->where('from_id', '=', $to)
                  ->where('to_id', '=', $from);
        })->get();
        $return =  ['chats' => $chats, 'to' => $toId , 'from' => $fromId];
        return response()->json(['status'=>true, 'data' => $return, 'message' => 'all chats']);
    }
}
