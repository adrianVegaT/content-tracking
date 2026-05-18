<?php

use App\Models\Channel;
use App\Models\Note;
use App\Models\State;
use App\Models\TelegramUser;
use App\Services\NoteService;
use App\Services\TranscriptionService;
use App\Telegram\Handlers\VoiceMessageHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Chat\Chat;
use SergiX44\Nutgram\Telegram\Types\Media\File;
use SergiX44\Nutgram\Telegram\Types\Media\Voice;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

uses(RefreshDatabase::class);

beforeEach(function () {
    State::firstOrCreate(['name' => 'capturing'], ['description' => 'Nota en captura']);
    State::firstOrCreate(['name' => 'pending'], ['description' => 'Nota pendiente de clasificar']);
    Channel::firstOrCreate(['name' => 'telegram'], ['description' => 'Canal de Telegram']);
});

afterEach(fn () => Mockery::close());

// /init tests

it('/init with unlinked account replies with not-linked message', function () {
    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => 99999, 'type' => 'private']));

    $bot->hearText('/init')->reply()->assertReplyText('Tu cuenta de Telegram no está vinculada.');
});

it('/init with linked account creates a capturing note and confirms', function () {
    $chatId = 12345;
    $telegramUser = TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('/init')->reply()->assertReplyText('Nota iniciada. Envía tus mensajes y termina con /finish.');

    $this->assertDatabaseHas('notes', [
        'user_id' => $telegramUser->user_id,
        'state_id' => State::where('name', 'capturing')->first()->id,
    ]);
});

it('/init is idempotent when a capturing note already exists', function () {
    $chatId = 12345;
    $telegramUser = TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);
    Note::factory()->capturing()->create(['user_id' => $telegramUser->user_id]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('/init')->reply()->assertReplyText('Ya tienes una nota activa. Continúa enviando mensajes y termina con /finish.');

    expect(Note::where('user_id', $telegramUser->user_id)->count())->toBe(1);
});

// Text message tests

it('text message with unlinked account replies with not-linked message', function () {
    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => 99999, 'type' => 'private']));

    $bot->hearText('hola')->reply()->assertReplyText('Tu cuenta de Telegram no está vinculada.');
});

it('text message without active note replies with no-active-note message', function () {
    $chatId = 12345;
    TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('hola')->reply()->assertReplyText('No tienes una nota activa. Usa /init para comenzar.');
});

it('text message with active note appends content and confirms', function () {
    $chatId = 12345;
    $telegramUser = TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);
    Note::factory()->capturing()->create(['user_id' => $telegramUser->user_id]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('mi texto')->reply();

    $note = Note::where('user_id', $telegramUser->user_id)->first();
    expect($note->content)->toBe('mi texto');
});

// /finish tests

it('/finish with unlinked account replies with not-linked message', function () {
    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => 99999, 'type' => 'private']));

    $bot->hearText('/finish')->reply()->assertReplyText('Tu cuenta de Telegram no está vinculada.');
});

it('/finish without active note replies with no-active-note message', function () {
    $chatId = 12345;
    TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('/finish')->reply()->assertReplyText('No tienes una nota activa para cerrar.');
});

it('/finish with active note moves it to pending and confirms', function () {
    $chatId = 12345;
    $telegramUser = TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);
    $note = Note::factory()->capturing()->create(['user_id' => $telegramUser->user_id]);

    Telegram::fake();
    $bot = app(Nutgram::class);
    $bot->setCommonChat(Chat::fromArray(['id' => $chatId, 'type' => 'private']));

    $bot->hearText('/finish')->reply()->assertReplyText('Nota guardada. Ya puedes revisarla en el sistema.');

    expect($note->fresh()->state->name)->toBe('pending');
});

// Voice message test

it('voice message is transcribed and appended to active note', function () {
    $chatId = 12345;
    $telegramUser = TelegramUser::factory()->create(['telegram_chat_id' => $chatId]);
    $note = Note::factory()->capturing()->create(['user_id' => $telegramUser->user_id]);

    $voice = Voice::fromArray(['file_id' => 'fake_file_id', 'file_unique_id' => 'fake_unique', 'duration' => 5]);
    $message = new Message;
    $message->voice = $voice;

    $transcriptionMock = Mockery::mock(TranscriptionService::class);
    $transcriptionMock->shouldReceive('transcribe')
        ->once()
        ->andReturn('nota de voz transcrita');

    $botMock = Mockery::mock(Nutgram::class);
    $botMock->shouldReceive('chatId')->andReturn($chatId);
    $botMock->shouldReceive('message')->andReturn($message);
    $botMock->shouldReceive('getFile')->andReturn(Mockery::mock(File::class));
    $botMock->shouldReceive('downloadFile')->andReturn(true);
    $botMock->shouldReceive('sendMessage')->never();

    $handler = new VoiceMessageHandler(app(NoteService::class), $transcriptionMock);
    ($handler)($botMock);

    expect($note->fresh()->content)->toBe('nota de voz transcrita');
});
