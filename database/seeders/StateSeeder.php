<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['name' => 'capturing',   'description' => 'Capturando mensajes'],
            ['name' => 'pending',     'description' => 'Pendiente de clasificar'],
            ['name' => 'classified',  'description' => 'Clasificada'],
            ['name' => 'in_progress', 'description' => 'En progreso'],
            ['name' => 'done',        'description' => 'Finalizada'],
            ['name' => 'published',   'description' => 'Publicada'],
            ['name' => 'discarded',   'description' => 'Descartada'],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(['name' => $state['name']], $state);
        }
    }
}
