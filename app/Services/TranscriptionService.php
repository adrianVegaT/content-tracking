<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TranscriptionService
{
    public function transcribe(string $audioPath): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->attach('file', file_get_contents($audioPath), basename($audioPath))
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
                'language' => 'es',
            ]);

        return $response->json('text', '');
    }
}
