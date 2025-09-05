
<!DOCTYPE html>
<html>
<head>
    <title>Informe PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #4338ca;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <h1>Informe de Análisis</h1>
    
    <p>Fecha de generación: {{ date('d/m/Y') }}</p>
    
    <h2>Datos del informe</h2>
    
    <table>
        <thead>
            <tr>
                <th>Ciudad</th>
                <th>Total Estudiantes</th>
                <th>Tests Completados</th>
                <th>Tasa Conversión</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($datos['tabla']) && count($datos['tabla']) > 0)
                @foreach($datos['tabla'] as $fila)
                <tr>
                    <td>{{ $fila['ciudad'] }}</td>
                    <td>{{ $fila['total_estudiantes'] }}</td>
                    <td>{{ $fila['tests_completados'] }}</td>
                    <td>{{ $fila['tasa_conversion'] }}%</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center;">No hay datos disponibles</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <h2>Insights y Recomendaciones</h2>
    <ul>
        <li>La ciudad de Medellín presenta la mayor tasa de conversión, sugiriendo un mayor nivel de compromiso con el test vocacional.</li>
        <li>Se recomienda fortalecer la campaña de difusión en Cali, donde se observa la menor tasa de conversión.</li>
    </ul>
</body>
</html>