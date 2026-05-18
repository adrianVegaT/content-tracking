<?php

namespace App\Telegram\Handlers;

use App\Services\NoteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SergiX44\Nutgram\Nutgram;

class InitNoteCommandHandler
{
    public function __construct(private NoteService $noteService) {}

    public function __invoke(Nutgram $bot): void
    {
        try {
            $user = $this->noteService->resolveUserFromChatId($bot->chatId());
        } catch (ModelNotFoundException) {
            $bot->sendMessage('Tu cuenta de Telegram no está vinculada.');

            return;
        }

        $note = $this->noteService->openDraftNote($user);

        $message = $note->wasRecentlyCreated
            ? 'Nota iniciada. Envía tus mensajes y termina con /finish.'
            : 'Ya tienes una nota activa. Continúa enviando mensajes y termina con /finish.';

        $bot->sendMessage($message);
    }
}
