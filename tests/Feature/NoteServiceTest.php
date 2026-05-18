<?php

use App\Models\Channel;
use App\Models\Note;
use App\Models\State;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\NoteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    State::firstOrCreate(['name' => 'capturing'], ['description' => 'Nota en captura']);
    State::firstOrCreate(['name' => 'pending'], ['description' => 'Nota pendiente de clasificar']);
    Channel::firstOrCreate(['name' => 'telegram'], ['description' => 'Canal de Telegram']);

    $this->service = app(NoteService::class);
    $this->user = User::factory()->create();
});

it('opens a capturing note for a user', function () {
    $note = $this->service->openDraftNote($this->user);

    expect($note)->toBeInstanceOf(Note::class)
        ->and($note->state->name)->toBe('capturing')
        ->and($note->wasRecentlyCreated)->toBeTrue();
});

it('does not duplicate capturing note if one already exists', function () {
    $this->service->openDraftNote($this->user);
    $this->service->openDraftNote($this->user);

    expect(Note::where('user_id', $this->user->id)->count())->toBe(1);
});

it('appends text with newline to active capturing note', function () {
    Note::factory()->capturing()->create([
        'user_id' => $this->user->id,
        'content' => 'primera línea',
    ]);

    $result = $this->service->appendTextToDraft($this->user, 'segunda línea');

    expect($result->fresh()->content)->toBe("primera línea\nsegunda línea");
});

it('returns null when appending text with no active note', function () {
    $result = $this->service->appendTextToDraft($this->user, 'texto');

    expect($result)->toBeNull();
});

it('closes capturing note and moves it to pending', function () {
    $note = Note::factory()->capturing()->create(['user_id' => $this->user->id]);

    $this->service->finishDraftNote($this->user);

    expect($note->fresh()->state->name)->toBe('pending');
});

it('returns null when finishing with no active note', function () {
    $result = $this->service->finishDraftNote($this->user);

    expect($result)->toBeNull();
});

it('resolves user from a known chat id', function () {
    TelegramUser::factory()->create([
        'user_id' => $this->user->id,
        'telegram_chat_id' => 99999,
    ]);

    $resolved = $this->service->resolveUserFromChatId(99999);

    expect($resolved->id)->toBe($this->user->id);
});

it('throws ModelNotFoundException for unknown chat id', function () {
    $this->service->resolveUserFromChatId(00000);
})->throws(ModelNotFoundException::class);
