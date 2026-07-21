<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaEstudiantilSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertar carreras en programas_academicos
        DB::table('programas_academicos')->insert([
            ['id_programa' => 1, 'nombre_programa' => 'Ingeniería de Sistemas'],
            ['id_programa' => 2, 'nombre_programa' => 'Tecnología en Gestión de Redes'],
            ['id_programa' => 3, 'nombre_programa' => 'Administración de Empresas'],
        ]);

        // 2. Insertar Directores de Unidad (Acoplado exactamente a tu DB)
        DB::table('directores_unidad')->insert([
            [
                'id_docente' => 1, 
                'nombre_director' => 'Director Ingeniería', 
                'correo_director' => 'ingenieria@gmail.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_docente' => 2, 
                'nombre_director' => 'Director Contaduría', 
                'correo_director' => 'contaduria@gmail.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_docente' => 3, 
                'nombre_director' => 'Director Agropecuaria', 
                'correo_director' => 'agropecuaria@gmail.com',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // 3. Insertar Estudiantes (Estructura optimizada y vinculada a id_docente)
        DB::table('estudiantes')->insert([
            [
                'codigo_estudiante' => 'EST-2026-001',
                'nombre_estudiante' => 'Juan Sebastián Zuluaga Barco',
                'correo'            => 'juan.zuluaga@cotecnova.edu.co',
                'id_programa'       => 1, 
                'id_docente'        => 1, // Vinculado a Director Ingeniería
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'codigo_estudiante' => 'EST-2026-002',
                'nombre_estudiante' => 'María Camila Restrepo Torres',
                'correo'            => 'camila.restrepo@cotecnova.edu.co',
                'id_programa'       => 2, 
                'id_docente'        => 2, // Vinculado a Director Contaduría
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        ]);

        // 4. Insertar riesgos de deserción
        DB::table('riesgos_desercion')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'nivel_riesgo' => 'Bajo', 'aplica_beca' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'nivel_riesgo' => 'Alto', 'aplica_beca' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Insertar estilos de vida
        DB::table('estilos_vida')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'estilo' => 'Saludable y Deporte Activo', 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'estilo' => 'Sedentario', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Insertar orientaciones psicológicas
        DB::table('orientaciones_psicologicas')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'nivel_servicio' => 'Preventivo y Tutoría', 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'nivel_servicio' => 'Intervención de Apoyo Emocional', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}