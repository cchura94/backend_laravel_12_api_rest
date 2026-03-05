<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Notas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .nota-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 3px;
        }

        .movimientos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .movimientos-table th,
        .movimientos-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .movimientos-table th {
            background: #f0f0f0;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
        }

        .tipo-compra {
            color: blue;
            font-weight: bold;
        }

        .tipo-venta {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>REPORTE GENERAL DE NOTAS</h2>
    <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
</div>

@foreach($notas as $nota)

@php
    $total = 0;
@endphp

<div class="nota-box">

    <table class="info-table">
        <tr>
            <td><strong>N° Nota:</strong> {{ $nota->id }}</td>
            <td><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>
                <strong>Tipo:</strong>
                <span class="{{ $nota->tipo_nota == 'compra' ? 'tipo-compra' : 'tipo-venta' }}">
                    {{ strtoupper($nota->tipo_nota) }}
                </span>
            </td>
            <td><strong>Estado:</strong> {{ strtoupper($nota->estado_nota) }}</td>
        </tr>
        <tr>
            <td><strong>Cliente/Proveedor:</strong> {{ $nota->cliente->razon_social }}</td>
            <td><strong>Documento:</strong> {{ $nota->cliente->nro_identificacion }}</td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong> {{ $nota->cliente->direccion }}</td>
            <td><strong>Usuario:</strong> {{ $nota->user->name }}</td>
        </tr>
    </table>

    <table class="movimientos-table">
        <thead>
            <tr>
                <th>Almacén</th>
                <th>Producto ID</th>
                <th>Cantidad</th>
                <th>P. Unitario</th>
                <th>Subtotal</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>

        @foreach($nota->movimientos as $mov)
            @php
                $precio = $nota->tipo_nota == 'compra'
                    ? $mov->pivot->precio_unitario_compra
                    : $mov->pivot->precio_unitario_venta;

                $subtotal = $mov->pivot->cantidad * $precio;
                $total += $subtotal;
            @endphp

            <tr>
                <td>{{ $mov->nombre }}</td>
                <td>{{ $mov->pivot->producto_id }}</td>
                <td>{{ $mov->pivot->cantidad }}</td>
                <td>{{ number_format($precio,2) }}</td>
                <td>{{ number_format($subtotal,2) }}</td>
                <td>{{ $mov->pivot->observaciones ?? '-' }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

    <div class="total">
        TOTAL NOTA: S/ {{ number_format($total,2) }}
    </div>

</div>

@endforeach

</body>
</html>