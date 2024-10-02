<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        return MessageResource::collection(Message::where('conversation_id', $conversation->id)->latest('sent_at')->cursorPaginate(10));
    }

public function store(MessageRequest $request)
{
    $validated = $request->validated();
    $validated['sender_id'] = $request->user()->id;

    if (empty($validated['conversation_id']) && empty($validated['receiver_id'])) {
        return response()->json(['error' => 'Receiver ID is required if conversation ID is not provided'], 422);
    }

    try {
        \DB::beginTransaction();

        if (empty($validated['conversation_id'])) {
            $conversation = Conversation::create();
            $validated['conversation_id'] = $conversation->id;
            User::find($validated['receiver_id'])->conversations()->attach($conversation->id);
            $request->user()->conversations()->attach($conversation->id);
        }

        $message = Message::create($validated);
        \DB::commit();

        return new MessageResource($message);
    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json(['error' => 'Failed to store message'], 500);
    }
}


    public function show(Message $message)
    {
        $this->authorize('view', $message);

        return new MessageResource($message);
    }

    public function update(MessageRequest $request, Message $message)
    {
        $this->authorize('update', $message);

        $message->update($request->validated());

        return new MessageResource($message);
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();

        return response()->json();
    }
}
