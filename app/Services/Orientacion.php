<?php

namespace App\Services;

use App\Models\OrientacionPsicologica;

class Orientacion
{
    public static function generarYGuardar($estudiante, array $respuestas)
    {
        $academico = $respuestas['afectacion_academico'] ?? 'Bajo';
        $socio     = $respuestas['afectacion_socioeconomico'] ?? 'Bajo';
        $psico     = $respuestas['afectacion_psicosocial'] ?? 'Bajo';

        // Determinar el Nivel de Servicio
        $nivelServicio = 'Tutoría Académica Standard';
        if (in_array('Alto', [$academico, $socio, $psico]) || in_array('Alta', [$academico, $socio, $psico])) {
            $nivelServicio = 'Atención Prioritaria Bienestar / Psicología';
        } elseif (in_array('Medio', [$academico, $socio, $psico]) || in_array('Media', [$academico, $socio, $psico])) {
            $nivelServicio = 'Acompañamiento Psicoeducativo Preventivo';
        }

        // Construir el texto explicativo de las observaciones
        $observaciones = "Orientación Automática PIAE:\n";
        $observaciones .= "- Afectación Académica: {$academico}\n";
        $observaciones .= "- Afectación Socioeconómica: {$socio}\n";
        $observaciones .= "- Afectación Psicosocial: {$psico}\n\n";
        $observaciones .= "Recomendación sugerida: " . self::obtenerRecomendacion($academico, $socio, $psico);

        // Guardar o Actualizar usando solo las columnas requeridas
        return OrientacionPsicologica::updateOrCreate(
            ['codigo_estudiante' => $estudiante->codigo_estudiante],
            [
                'nivel_servicio' => $nivelServicio,
                'observaciones'  => $observaciones,
            ]
        );
    }

    private static function obtenerRecomendacion($academico, $socio, $psico)
    {
        // Lógica de recomendación según combinaciones
        if ($academico === 'Alto' || $academico === 'Alta') {
            return "Remitir a talleres de hábitos de estudio y tutorías con docentes de área.";
        }
        if ($psico === 'Alto' || $psico === 'Alta') {
            return "Programar sesión individual con el equipo de Psicología / Bienestar Institucional.";
        }
        if ($socio === 'Alto' || $socio === 'Alta') {
            return "Informar sobre convocatorias de apoyos socioeconómicos y subsidios de la institución.";
        }

        return "Mantener seguimiento regular en el módulo de monitoreo.";
    }
}