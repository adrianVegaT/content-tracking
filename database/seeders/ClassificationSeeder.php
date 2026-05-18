<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['insight', 'decision', 'historia'] as $name) {
            Classification::firstOrCreate(['name' => $name]);
        }
    }
}
