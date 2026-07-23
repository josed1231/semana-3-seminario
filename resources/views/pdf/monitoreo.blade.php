<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Monitoreo de Estudiantes - COTECNOVA</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #0f172a; }
        .subheader { text-align: center; font-size: 11px; color: #64748b; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0d6efd; color: white; padding: 8px 6px; font-size: 10px; text-align: left; text-transform: uppercase; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; color: white; font-size: 9px; display: inline-block; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-success { background-color: #198754; }
        .bg-secondary { background-color: #6c757d; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">COTECNOVA - Reporte Institucional de Monitoreo</div>
    <div class="subheader">Generado el: {{ date('d/m/Y H:i') }} | Generado por: {{ auth()->user()->name ?? auth()->user()->username }}</div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Estudiante</th>
                <th>Programa</th>
                <th>Jornada</th>
                <th>Riesgo</th>
                <th>Servicio / Orientación</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estudiantes as $e)
                <tr>
                    <td><strong>{{ $e->codigo_estudiante }}</strong></td>
                    <td>{{ $e->nombre_estudiante }}</td>
                    <td>{{ $e->programa->nombre_programa ?? 'Sin Asignar' }}</td>
                    <td>{{ ucfirst($e->jornada ?? 'N/A') }}</td>
                    <td>
                        @php
                            $nivel = $e->riesgo->nivel_riesgo ?? 'Sin evaluar';
                            $clase = match($nivel) {
                                'Alto' => 'bg-danger',
                                'Medio' => 'bg-warning',
                                'Bajo' => 'bg-success',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $clase }}">{{ $nivel }}</span>
                    </td>
                    <td>{{ $e->orientacionPsicologica->nivel_servicio ?? 'N/A' }}</td>
                    <td>{{ $e->orientacionPsicologica->observaciones ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">No se encontraron registros con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Información para la Detección Temprana de la Deserción Estudiantil - COTECNOVA
    </div>
</body>
</html>