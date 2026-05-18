<?php

namespace App\Telegram\Handlers;

use App\Services\NoteService;
use App\Services\TranscriptionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SergiX44\Nutgram\Nutgram;

class VoiceMessageHandler
{
    public function __construct(
        private NoteService $noteService,
        private TranscriptionService $transcriptionService,
    ) {}

    public function __invoke(Nutgram $bot): void
    {
        try {
            $user = $this->noteService->resolveUserFromChatId($bot->chatId());
        } catch (ModelNotFoundException) {
            $bot->sendMessage('Tu cuenta de Telegram no está vinculada.');

            return;
        }

        $voice = $bot->message()->voice;
        $file = $bot->getFile($voice->file_id);
        $tempPath = storage_path('app/temp/voice_'.$bot->chatId().'_'.time().'.ogg');

        $bot->downloadFile($file, $tempPath);

        try {
            $text = $this->transcriptionService->transcribe($tempPath);
        } catch (\Throwable) {
            $bot->sendMessage('No pude transcribir el audio. Intenta de nuevo.');

            return;
        } finally {
            @unlink($tempPath);
        }

        if (empty($text)) {
            $bot->sendMessage('No pude transcribir el audio. Intenta de nuevo.');

            return;
        }

        $note = $this->noteService->appendTextToDraft($user, $text);

        if ($note === null) {
            $bot->sendMessage('No tienes una nota activa. Usa /init para comenzar.');

            return;
        }

    }
}
