<?php

namespace App\Telegram\Handlers;

use App\Services\NoteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SergiX44\Nutgram\Nutgram;

class FinishNoteCommandHandler
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

        $note = $this->noteService->finishDraftNote($user);

        if ($note === null) {
            $bot->sendMessage('No tienes una nota activa para cerrar.');

            return;
        }

        $bot->sendMessage('Nota guardada. Ya puedes revisarla en el sistema.');
    }
}
