<?php

/** @var Nutgram $bot */

use App\Telegram\Handlers\FinishNoteCommandHandler;
use App\Telegram\Handlers\InitNoteCommandHandler;
use App\Telegram\Handlers\TextMessageHandler;
use App\Telegram\Handlers\VoiceMessageHandler;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

$bot->onCommand('init', InitNoteCommandHandler::class)
    ->description('Inicia una nueva nota');

$bot->onCommand('finish', FinishNoteCommandHandler::class)
    ->description('Cierra y guarda la nota en curso');

$bot->onVoice(VoiceMessageHandler::class);

$bot->onText('[^/].*', TextMessageHandler::class);
