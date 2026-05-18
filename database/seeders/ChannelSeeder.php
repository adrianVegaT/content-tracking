<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        Channel::firstOrCreate(['name' => 'telegram'], ['description' => 'Canal de Telegram']);
        Channel::firstOrCreate(['name' => 'web'], ['description' => 'Canal web']);
    }
}
