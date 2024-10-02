<?php

namespace App\Http\Resources;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sent_at' => $this->sent_at,
            'read_at' => $this->read_at,
            'reader' => $this->reader,
            'content' => $this->content,
            'sender_id' => $this->sender_id,
            'conversation_id' => $this->conversation_id,

            'conversation' => new ConversationResource($this->whenLoaded('conversation')),
        ];
    }
}
