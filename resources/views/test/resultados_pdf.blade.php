<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 30px;
            color: #222;
            position: relative;
            line-height: 1.4;
            font-size: 12px;
        }
        .logo-institucional {
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: auto;
            margin-top: -35px;
            margin-right: 10px;
        }
        h1 {
            color: #2a4365;
            font-size: 24px;
            margin: 0 0 8px 0;
            padding: 0;
            border-bottom: 2px solid #2a4365;
            padding-bottom: 4px;
            text-align: center;
            font-weight: bold;
        }
        .subtitulo {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
        h2 {
            color: #2b6cb0;
            margin-top: 25px;
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: bold;
        }
        .info {
            margin-bottom: 15px;
            background: #f7fafc;
            padding: 10px;
            border-radius: 6px;
            border-left: 4px solid #4299e1;
        }
        .info strong {
            color: #2b6cb0;
        }
        .perfil-list {
            margin-bottom: 15px;
        }
        .perfil-list li {
            margin-bottom: 6px;
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            background: #bee3f8;
            color: #2b6cb0;
            font-size: 10px;
            font-weight: bold;
            margin-right: 8px;
            text-transform: uppercase;
        }
        .badge-principal {
            background: #c6f6d5;
            color: #22543d;
        }
        .badge-acreditada {
            background: #bee3f8;
            color: #2c5282;
        }
        .match {
            font-weight: bold;
            color: #2563eb;
            font-size: 12px;
            background: #ebf8ff;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .sec {
            color: #64748b;
            font-size: 11px;
        }
        .desc {
            font-size: 11px;
            color: #444;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            text-align: left;
        }
        th {
            background-color: #e2e8f0;
            color: #2a4365;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr.principal {
            background: #e0f2fe;
        }
        tr:nth-child(even):not(.principal) {
            background-color: #f8fafc;
        }
        .universidad-list {
            margin: 0;
            padding-left: 14px;
        }
        .universidad-list li {
            font-size: 11px;
            margin-bottom: 3px;
            color: #4a5568;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            background: #f7fafc;
            padding: 10px;
            border-radius: 6px;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 15px;
            background: #f7fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <img src="{{ public_path('images/LOGO_UNO.png') }}" alt="Logo institucional" class="logo-institucional">
    <h1>{{ $titulo }}</h1>
    <div class="subtitulo">Reporte personalizado de orientación vocacional</div>
    <div class="info">
        <strong>Estudiante:</strong> {{ $test->user->name ?? 'N/A' }}<br>
        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($test->fecha_completado)->setTimezone('America/La_Paz')->format('d/m/Y H:i') }} <span class="sec"></span>
    </div>

    <h2>Tu perfil RIASEC</h2>
    @if(!empty($resultados['porcentajes']))
        <ul class="perfil-list">
            @php
                $porcentajes = $resultados['porcentajes'];
                arsort($porcentajes); // Ordenar por dominancia
            @endphp
            @foreach($porcentajes as $tipo => $valor)
                <li>
                    <span class="badge">{{ $tipo }}</span>
                    <strong>{{ $valor }}%</strong>
                    <span class="sec">{{ $tiposPersonalidad[$tipo] ?? 'Descripción no disponible' }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data">No se encontraron datos de perfil RIASEC.</div>
    @endif

    <h2>Carreras principales recomendadas</h2>
    @php
        $carrerasPrincipales = $resultados['recomendaciones']['afines'] ?? [];
    @endphp
    @if(count($carrerasPrincipales) > 0)
        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Área</th>
                    <th>Compatibilidad</th>
                    <th>Universidades</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carrerasPrincipales as $carrera)
                    <tr class="principal">
                        <td>
                            <strong>{{ $carrera['nombre'] ?? 'Nombre no disponible' }}</strong>
                            <span class="badge badge-principal">Principal</span>
                            <div class="desc">{{ $carrera['descripcion'] ?? 'Descripción no disponible' }}</div>
                        </td>
                        <td>{{ $carrera['area'] ?? 'Área no disponible' }}</td>
                        <td><span class="match">{{ $carrera['score'] ?? 0 }}%</span></td>
                        <td>
                            @if(!empty($carrera['universidades']))
                                <ul class="universidad-list">
                                    @foreach($carrera['universidades'] as $uni)
                                        <li>
                                            {{ is_array($uni) ? ($uni['nombre'] ?? 'Nombre no disponible') : ($uni->nombre ?? 'Nombre no disponible') }}
                                            @if((is_array($uni) ? ($uni['acreditada'] ?? false) : ($uni->acreditada ?? false)))
                                                <span class="badge badge-acreditada">Acreditada</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="sec">Sin universidades registradas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No se encontraron carreras principales para tu perfil.</div>
    @endif

    <h2>Otras carreras relacionadas</h2>
    @php
        $carrerasRelacionadas = $resultados['recomendaciones']['relacionadas'] ?? [];
    @endphp
    @if(count($carrerasRelacionadas) > 0)
        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Área</th>
                    <th>Compatibilidad</th>
                    <th>Universidades</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carrerasRelacionadas as $carrera)
                    <tr>
                        <td>
                            <strong>{{ $carrera['nombre'] ?? 'Nombre no disponible' }}</strong>
                            <div class="desc">{{ $carrera['descripcion'] ?? 'Descripción no disponible' }}</div>
                        </td>
                        <td>{{ $carrera['area'] ?? 'Área no disponible' }}</td>
                        <td><span class="match">{{ $carrera['score'] ?? 0 }}%</span></td>
                        <td>
                            @if(!empty($carrera['universidades']))
                                <ul class="universidad-list">
                                    @foreach($carrera['universidades'] as $uni)
                                        <li>
                                            {{ is_array($uni) ? ($uni['nombre'] ?? 'Nombre no disponible') : ($uni->nombre ?? 'Nombre no disponible') }}
                                            @if((is_array($uni) ? ($uni['acreditada'] ?? false) : ($uni->acreditada ?? false)))
                                                <span class="badge badge-acreditada">Acreditada</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="sec">Sin universidades registradas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No se encontraron otras carreras relacionadas para tu perfil.</div>
    @endif

    {{-- Depuración temporal: Quita esto después --}}
    {{-- {{ dd($resultados['recomendaciones']['afines'][0]['score'] ?? 'No score') }} --}}

    <div class="footer">
        <strong>Nota Importante:</strong> Este informe fue generado automáticamente por el sistema de orientación vocacional de la Universidad Nacional de Oriente.<br>
        Los resultados se basan en tu perfil RIASEC y pueden variar según factores personales. Para una mejor interpretación, consulta con un orientador profesional.<br>
        <strong>Fecha de Generación:</strong> {{ now('America/La_Paz')->format('d/m/Y H:i') }}
    </div>
</body>
</html>