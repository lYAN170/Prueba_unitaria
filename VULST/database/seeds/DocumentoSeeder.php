<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentoSeeder extends Seeder
{
    public function run()
    {
        DB::table('documentos')->insert([
            ['tipo_documento' => 'DNI'],
            ['tipo_documento' => 'Pasaporte'],
            ['tipo_documento' => 'RUC'],
            ['tipo_documento' => 'Carnet Extranjería'],
        ]);
    }
}

