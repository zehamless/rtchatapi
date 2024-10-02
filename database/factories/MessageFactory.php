<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'sent_at' => Carbon::now(),
            'read_at' => Carbon::now(),
            'reader' => $this->faker->words(),
            'content' => $this->faker->text(),
            'sender_id' => User::factory(),
            'conversation_id' => Conversation::factory(),
        ];
    }
}
