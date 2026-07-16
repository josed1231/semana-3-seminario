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

        // 2. Insertar docentes
        DB::table('docentes')->insert([
            ['id_docente' => 1, 'nombre_docente' => 'Ing. Carlos Mario Restrepo'],
            ['id_docente' => 2, 'nombre_docente' => 'Dra. Liliana Patricia Cardona'],
            ['id_docente' => 3, 'nombre_docente' => 'Msc. Alejandro Zuluaga Giraldo'],
        ]);

        // 3. Insertar Actividades
        DB::table('actividades')->insert([
            ['id_actividad' => 1, 'nombre_actividad' => 'Semillero de Inteligencia Artificial'],
            ['id_actividad' => 2, 'nombre_actividad' => 'Torneo de Microfútbol Interclases'],
            ['id_actividad' => 3, 'nombre_actividad' => 'Club de Programación Competitiva'],
        ]);

        // 4. Insertar estudiantes reales para la simulación
        DB::table('estudiantes')->insert([
            [
                'codigo_estudiante' => 'EST-2026-001',
                'nombre_estudiante' => 'Juan Sebastián Zuluaga Barco',
                'correo' => 'juan.zuluaga@cotecnova.edu.co',
                'id_programa' => 1, // Ing de Sistemas
                'id_docente' => 1,   // Carlos Mario
                'valor_matricula' => 1250000.00,
                'estado_pago' => 'Pagado',
                'promedio' => 4.85,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_estudiante' => 'EST-2026-002',
                'nombre_estudiante' => 'María Camila Restrepo Torres',
                'correo' => 'camila.restrepo@cotecnova.edu.co',
                'id_programa' => 2, // Gestión de Redes
                'id_docente' => 2,   // Liliana Cardona
                'valor_matricula' => 980000.00,
                'estado_pago' => 'Pendiente',
                'promedio' => 3.20,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 5. Insertar riesgos de deserción
        DB::table('riesgos_desercion')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'nivel_riesgo' => 'Bajo', 'aplica_beca' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'nivel_riesgo' => 'Alto', 'aplica_beca' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Insertar estilos de vida
        DB::table('estilos_vida')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'estilo' => 'Saludable y Deporte Activo', 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'estilo' => 'Sedentario', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Insertar orientaciones psicológicas
        DB::table('orientaciones_psicologicas')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'nivel_servicio' => 'Preventivo y Tutoría', 'created_at' => now(), 'updated_at' => now()],
            ['codigo_estudiante' => 'EST-2026-002', 'nivel_servicio' => 'Intervención de Apoyo Emocional', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 8. Insertar asignación de actividades (Tabla pivot)
        DB::table('estudiante_actividad')->insert([
            ['codigo_estudiante' => 'EST-2026-001', 'id_actividad' => 1, 'created_at' => now(), 'updated_at' => now()], // Juan en Semillero IA
            ['codigo_estudiante' => 'EST-2026-001', 'id_actividad' => 3, 'created_at' => now(), 'updated_at' => now()], // Juan en Club Prog
            ['codigo_estudiante' => 'EST-2026-002', 'id_actividad' => 2, 'created_at' => now(), 'updated_at' => now()], // Camila en Microfútbol
        ]);
    }
}