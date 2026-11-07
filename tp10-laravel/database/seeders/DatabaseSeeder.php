<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos iniciales del módulo.
     */
    public function run(): void
    {
        $this->call(ServicioSeeder::class);
    }
}
