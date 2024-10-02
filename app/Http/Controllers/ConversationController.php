<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;

class ConversationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Conversation::class);
        $request->user()->id;
        return ConversationResource::collection(Conversation::all());
    }

    public function store(ConversationRequest $request)
    {
        $this->authorize('create', Conversation::class);
        return new ConversationResource(Conversation::create($request->validated()));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        return new ConversationResource($conversation);
    }

    public function update(ConversationRequest $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $conversation->update($request->validated());

        return new ConversationResource($conversation);
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        $conversation->delete();

        return response()->json();
    }
}
