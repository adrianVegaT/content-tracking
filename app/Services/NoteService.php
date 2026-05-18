<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Note;
use App\Models\State;
use App\Models\TelegramUser;
use App\Models\User;

class NoteService
{
    public function resolveUserFromChatId(int $chatId): User
    {
        return TelegramUser::where('telegram_chat_id', $chatId)
            ->firstOrFail()
            ->user;
    }

    public function openDraftNote(User $user): Note
    {
        $capturingState = State::where('name', 'capturing')->firstOrFail();
        $telegramChannel = Channel::firstOrCreate(
            ['name' => 'telegram'],
            ['description' => 'Canal de Telegram']
        );

        return Note::firstOrCreate(
            ['user_id' => $user->id, 'state_id' => $capturingState->id],
            ['channel_id' => $telegramChannel->id, 'content' => null]
        );
    }

    public function appendTextToDraft(User $user, string $text): ?Note
    {
        $capturingState = State::where('name', 'capturing')->firstOrFail();
        $note = $user->notes()->where('state_id', $capturingState->id)->first();

        if ($note === null) {
            return null;
        }

        $note->content = $note->content !== null
            ? $note->content."\n".$text
            : $text;
        $note->save();

        return $note;
    }

    public function finishDraftNote(User $user): ?Note
    {
        $capturingState = State::where('name', 'capturing')->firstOrFail();
        $note = $user->notes()->where('state_id', $capturingState->id)->first();

        if ($note === null) {
            return null;
        }

        $note->update([
            'state_id' => State::where('name', 'pending')->firstOrFail()->id,
        ]);

        return $note;
    }
}
