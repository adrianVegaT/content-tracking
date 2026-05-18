<?php

namespace App\Telegram\Handlers;

use App\Services\NoteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SergiX44\Nutgram\Nutgram;

class TextMessageHandler
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

        $text = $bot->message()->text ?? '';
        $note = $this->noteService->appendTextToDraft($user, $text);

        if ($note === null) {
            $bot->sendMessage('No tienes una nota activa. Usa /init para comenzar.');

            return;
        }

    }
}
