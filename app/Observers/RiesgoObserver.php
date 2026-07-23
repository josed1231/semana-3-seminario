<?php

namespace App\Observers;

use App\Models\RiesgoDesercion;
use App\Services\Orientacion;

class RiesgoDesercionObserver
{
    /**
     * Handle the RiesgoDesercion "saved" event (se ejecuta tanto al crear como al actualizar el riesgo).
     */
    public function saved(RiesgoDesercion $riesgoDesercion): void
    {
        // Obtener el estudiante asociado mediante la relación
        $estudiante = $riesgoDesercion->estudiante;

        if ($estudiante) {
            $respuestas = [];

            // Buscar los niveles de afectación registrados en saberes_previos
            if ($estudiante->saberesPrevios && !empty($estudiante->saberesPrevios->respuestas)) {
                $rawJson = $estudiante->saberesPrevios->respuestas;
                $decoded = is_string($rawJson) ? json_decode($rawJson, true) : $rawJson;

                $respuestas = [
                    'afectacion_academico'     => $decoded['afectacion_academico'] ?? 'Bajo',
                    'afectacion_socioeconomico' => $decoded['afectacion_socioeconomico'] ?? 'Bajo',
                    'afectacion_psicosocial'    => $decoded['afectacion_psicosocial'] ?? 'Bajo',
                ];
            }

            // Generar y guardar la orientación automática
            Orientacion::generarYGuardar($estudiante, $respuestas);
        }
    }

    /**
     * Handle the RiesgoDesercion "deleted" event.
     */
    public function deleted(RiesgoDesercion $riesgoDesercion): void
    {
        //
    }
}