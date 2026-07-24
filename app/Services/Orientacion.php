<?php

namespace App\Services;

use App\Models\OrientacionPsicologica;

class Orientacion
{
    public static function generarYGuardar($estudiante, array $respuestas = [])
    {
        $academico = $respuestas['afectacion_academico'] ?? 'Bajo';
        $socio     = $respuestas['afectacion_socioeconomico'] ?? 'Bajo';
        $psico     = $respuestas['afectacion_psicosocial'] ?? 'Bajo';

        // Helpers para clasificar el nivel de riesgo
        $esAlto = function($val) {
            return in_array(strtolower(trim((string)$val)), ['alto', 'alta', 'critico', 'crítica', '3']);
        };

        $esMedio = function($val) {
            return in_array(strtolower(trim((string)$val)), ['medio', 'media', '2']);
        };

        // 1. Determinar el Nivel de Servicio Institucional PIAE
        $nivelServicio = 'Tutoría Académica Standard PIAE';
        if ($esAlto($academico) || $esAlto($socio) || $esAlto($psico)) {
            $nivelServicio = 'Atención Prioritaria PIAE / Bienestar Institucional';
        } elseif ($esMedio($academico) || $esMedio($socio) || $esMedio($psico)) {
            $nivelServicio = 'Acompañamiento Psicoeducativo Preventivo PIAE';
        }

        // 2. Construir la Observación Estructurada del PIAE
        $observaciones = "========================================\n";
        $observaciones .= "   DIAGNÓSTICO DE ORIENTACIÓN AUTOMÁTICA PIAE\n";
        $observaciones .= "========================================\n\n";
        $observaciones .= "Niveles de Afectación Detectados:\n";
        $observaciones .= "• Exigencias Académicas: {$academico}\n";
        $observaciones .= "• Afectación Socioeconómica: {$socio}\n";
        $observaciones .= "• Estrés / Psicosocial: {$psico}\n\n";
        $observaciones .= "Ruta de Atención e Intervención Sugerida PIAE:\n";
        $observaciones .= self::obtenerRecomendacion($academico, $socio, $psico);

        // 3. Guardar o Actualizar en la base de datos
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
        $rutas = [];

        $esRiesgo = function($val) {
            return in_array(strtolower(trim((string)$val)), ['alto', 'alta', 'medio', 'media', 'critico', 'crítica', '2', '3']);
        };

        // 1. Remisión Psicosocial -> Área de Psicología / Bienestar
        if ($esRiesgo($psico)) {
            $rutas[] = "• [PIAE - Psicología]: Remisión para valoración psicosocial, manejo de ansiedad/estrés y acompañamiento emocional.";
        }

        // 2. Remisión Socioeconómica -> Apoyos Institucionales
        if ($esRiesgo($socio)) {
            $rutas[] = "• [PIAE - Apoyo Socioeconómico]: Asesoría en subsidios, convenios de financiación, becas e incentivos de permanencia.";
        }

        // 3. Remisión Académica -> Tutorías e Intervención Pedagógica
        if ($esRiesgo($academico)) {
            $rutas[] = "• [PIAE - Acompañamiento Académico]: Vinculación a tutorías pares, talleres de hábitos de estudio y nivelación.";
        }

        // Si no presenta vulnerabilidades registradas
        if (empty($rutas)) {
            return "• [PIAE - Monitoreo Pasivo]: Mantener seguimiento periódico en la plataforma sin requerir remisión prioritaria.";
        }

        return implode("\n", $rutas);
    }
}