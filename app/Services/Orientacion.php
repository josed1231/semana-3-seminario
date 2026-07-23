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

        // Helper para validar niveles de riesgo
        $esAlto = function($val) {
            return in_array(strtolower($val), ['alto', 'alta', 'critico', 'crítica']);
        };

        $esMedio = function($val) {
            return in_array(strtolower($val), ['medio', 'media']);
        };

        // Determinar el Nivel de Servicio
        $nivelServicio = 'Tutoría Académica Standard';
        if ($esAlto($academico) || $esAlto($socio) || $esAlto($psico)) {
            $nivelServicio = 'Atención Prioritaria Bienestar / Psicología';
        } elseif ($esMedio($academico) || $esMedio($socio) || $esMedio($psico)) {
            $nivelServicio = 'Acompañamiento Psicoeducativo Preventivo';
        }

        // Construir el texto explicativo de las observaciones
        $observaciones = "Orientación Automática PIAE:\n";
        $observaciones .= "- Exigencias Académicas: {$academico}\n";
        $observaciones .= "- Afectación Socioeconómica: {$socio}\n";
        $observaciones .= "- Estrés / Psicosocial: {$psico}\n\n";
        $observaciones .= "Ruta de Atención Sugerida:\n";
        $observaciones .= self::obtenerRecomendacion($academico, $socio, $psico);

        // Guardar o Actualizar en la base de datos
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
            return in_array(strtolower($val), ['alto', 'alta', 'medio', 'media', 'critico', 'crítica']);
        };

        // 1. Temas de Estrés / Psicosocial -> Área de Psicología
        if ($esRiesgo($psico)) {
            $rutas[] = "• Área de Psicología: Remisión para manejo de estrés, salud mental y acompañamiento emocional.";
        }

        // 2. Temas Socioeconómicos -> Área Financiera
        if ($esRiesgo($socio)) {
            $rutas[] = "• Área Financiera: Orientación en apoyos económicos, opciones de pago, becas o subsidios.";
        }

        // 3. Exigencias Académicas -> Área de Bienestar
        if ($esRiesgo($academico)) {
            $rutas[] = "• Área de Bienestar: Acercamiento para nivelación por exigencias académicas, hábitos de estudio y tutorías.";
        }

        // Si no presenta afectaciones medias ni altas
        if (empty($rutas)) {
            return "• Módulo de Monitoreo: Mantener seguimiento regular en el sistema sin remisión prioritaria.";
        }

        return implode("\n", $rutas);
    }
}