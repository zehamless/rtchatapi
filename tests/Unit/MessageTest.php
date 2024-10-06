<?php

use App\Events\MessageSentEvent;
use App\Http\Requests\MessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

uses(TestCase::class, RefreshDatabase::class, WithFaker::class);

it('stores message successfully', function () {
    $user = User::factory()->create();
    $receiver = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'receiver_id' => $receiver->id,
        'content' => 'Hello, World!',
    ];

    $response = $this->postJson(route('messages.store'), $data);
    $response->assertStatus(201)
        ->assertJsonStructure(['data' => ['id', 'content', 'sender_id', 'conversation_id']]);
});

it('creates new conversation when storing message', function () {
    $user = User::factory()->create();
    $receiver = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'receiver_id' => $receiver->id,
        'content' => 'Hello, World!',
    ];

    $response = $this->postJson(route('messages.store'), $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('conversations', ['id' => $response->json('data.conversation_id')]);
});

it('uses existing conversation when storing message', function () {
    $user = User::factory()->create();
    $receiver = User::factory()->create();
    $conversation = Conversation::create();
    $user->conversations()->attach($conversation->id);
    $receiver->conversations()->attach($conversation->id);
    $this->actingAs($user);

    $data = [
        'receiver_id' => $receiver->id,
        'content' => 'Hello, World!',
        'conversation_id' => $conversation->id,
    ];

    $response = $this->postJson(route('messages.store'), $data);
//dd($response->getContent());
    $response->assertStatus(201);
    expect($response->json('data.conversation_id'))->toBe($conversation->id);
});

it('fails to store message without receiver_id', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'content' => 'Hello, World!',
    ];

    $response = $this->postJson(route('messages.store'), $data);
//    dd($response->getOriginalContent());
    $response->assertStatus(422);
});

it('fails to store message without content', function () {
    $user = User::factory()->create();
    $receiver = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'receiver_id' => $receiver->id,
    ];

    $response = $this->postJson(route('messages.store'), $data);

    $response->assertStatus(422);
});
it('deletes a message successfully', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create(['sender_id' => $user->id,
        'conversation_id' => Conversation::factory()->create()->id,
        'content' => 'Hello, World!']);
    $this->actingAs($user);

    $response = $this->deleteJson(route('messages.destroy', $message->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('messages', ['id' => $message->id]);
});

it('returns unauthorized when deleting a message without permission', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'sender_id' => User::factory()->create()->id,
        'conversation_id' => Conversation::factory()->create()->id,
        'content' => 'Hello, World!'
    ]);
    $this->actingAs($user);

    $response = $this->deleteJson(route('messages.destroy', $message->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('messages', ['id' => $message->id]);
});

it('returns not found when deleting a non-existent message', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->deleteJson(route('messages.destroy', 999));

    $response->assertStatus(404);
});
it('paginates messages for a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $user->conversations()->attach($conversation->id);
    $this->actingAs($user);

    Message::factory()->count(15)->create(['conversation_id' => $conversation->id]);

    $response = $this->getJson(route('messages.index', $conversation->id));

    $response->assertStatus(200)
        ->assertJsonStructure(['data' => [['id', 'content', 'sender_id', 'conversation_id']]]);
    expect(count($response->json('data')))->toBe(10);
});

it('returns empty data when no messages exist for a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $user->conversations()->attach($conversation->id);
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', $conversation->id));

    $response->assertStatus(200)
        ->assertJsonStructure(['data'])
        ->assertJsonCount(0, 'data');
});

it('returns unauthorized when fetching messages without permission', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', $conversation->id));

    $response->assertStatus(403);
});

it('returns not found when fetching messages for a non-existent conversation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', 999));

    $response->assertStatus(404);
});
it('cursor paginates messages for a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $user->conversations()->attach($conversation->id);
    $this->actingAs($user);

    Message::factory()->count(15)->create(['conversation_id' => $conversation->id]);

    $response = $this->getJson(route('messages.index', $conversation->id));
//dd($response->getContent());
    $response->assertStatus(200)
        ->assertJsonStructure(['data' => [['id', 'content', 'sender_id', 'conversation_id']], 'links'=> ['next']]);
    expect(count($response->json('data')))->toBe(10);
});

it('returns empty data when no messages exist for a conversation with cursor pagination', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $user->conversations()->attach($conversation->id);
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', $conversation->id));
//dd($response->getContent());
    $response->assertStatus(200)
        ->assertJsonStructure(['data', 'links'=> ['next']])
        ->assertJsonCount(0, 'data');
});

it('returns unauthorized when fetching messages without permission with cursor pagination', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', $conversation->id));

    $response->assertStatus(403);
});

it('returns not found when fetching messages for a non-existent conversation with cursor pagination', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('messages.index', 999));

    $response->assertStatus(404);
});
it('broadcasts MessageSentEvent when a message is stored', function () {
    Event::fake();

    $user = User::factory()->create();
    $receiver = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('messages.store'), [
        'content' => 'Hello, World!',
        'receiver_id' => $receiver->id,
    ]);
//dd($response->getContent());
    $response->assertStatus(201);
    Event::assertDispatched(MessageSentEvent::class);
});

it('does not broadcast MessageSentEvent when storing a message fails', function () {
    Event::fake();

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('messages.store'), [
        'content' => 'Hello, World!',
    ]);

    $response->assertStatus(422);
    Event::assertNotDispatched(MessageSentEvent::class);
});
it('creates group conversation successfully', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'name' => 'Group Chat',
        'description' => 'A new group chat',
    ];

    $response = $this->postJson(route('conversations.store'), $data);
//dd($response->getContent());
    $response->assertStatus(201);
    $this->assertDatabaseHas('conversations', ['name' => 'Group Chat', 'is_group' => true]);
    $this->assertDatabaseHas('user_conversations', ['user_id' => $user->id]);
});


it('fails to create group conversation when not authenticated', function () {
    $data = [
        'name' => 'Group Chat',
        'description' => 'A new group chat',
    ];

    $response = $this->postJson(route('conversations.store'), $data);

    $response->assertStatus(401);
});
