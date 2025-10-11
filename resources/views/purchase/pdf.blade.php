@php
use Carbon\Carbon;
/* use SimpleSoftwareIO\QrCode\Facades\QrCode; */

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;



@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra #{{ $purchase->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0a5275;
            padding-bottom: 10px;
        }
        .header img {
            width: 120px;
            height: auto;
        }
        .header h2 {
            color: #0a5275;
            margin: 0;
        }
        .info-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .products {
            margin-top: 20px;
        }
        .products table {
            width: 100%;
            border-collapse: collapse;
        }
        .products th, .products td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .products th {
            background-color: #f4f4f4;
            color: #0a5275;
        }
        .totals {
            margin-top: 15px;
            text-align: right;
        }
        .qr-section {
            margin-top: 30px;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div>
            <h2>Factura de Compra</h2>
            <p><strong>Compra #{{ $purchase->id }}</strong></p>
            <p>Fecha: {{ Carbon::parse($purchase->purchase_datetime)->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <strong>Proveedor:</strong> {{ $purchase->supplier->name ?? 'N/A' }}<br>
                    <strong>Email:</strong> {{ $purchase->supplier->email ?? 'N/A' }}<br>
                    <strong>Teléfono:</strong> {{ $purchase->supplier->phone ?? 'N/A' }}<br>
                    <strong>Dirección:</strong> {{ $purchase->supplier->address ?? 'N/A' }}
                </td>
                <td width="50%">
                    <strong>Usuario:</strong> {{ $purchase->user->name ?? 'N/A' }}<br>
                    <strong>Email:</strong> {{ $purchase->user->email ?? 'N/A' }}<br>
                    <strong>Tipo de documento:</strong> {{ $purchase->documentType->name ?? 'N/A' }}<br>
                    <strong>Estado:</strong>
                    @php
                        $statusColors = [
                            'pending' => 'color: orange;',
                            'completed' => 'color: green;',
                            'cancelled' => 'color: red;'
                        ];
                    @endphp
                    <span style="{{ $statusColors[$purchase->status] ?? '' }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="products">
        <h3>Detalles de la Compra</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->purchaseDetails as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->product->name ?? 'N/A' }}</td>
                        <td>{{ $detail->product->category->name ?? 'N/A' }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>${{ number_format($detail->unit_cost, 2) }}</td>
                        <td>${{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <h3>Total de la compra: ${{ number_format($purchase->total_cost, 2) }}</h3>
    </div>

    <div class="qr-section">
        <p>Escanea este código para ver el documento en línea:</p>
        <div>
            {{    }}
        </div>
        <p><a href="{{ $pdfUrl }}" target="_blank">{{ $pdfUrl }}</a></p>
    </div>

    <div class="footer">
        <p>Generado automáticamente por el Sistema de Compras — {{ config('app.name') }}</p>
        <p>© {{ date('Y') }} Todos los derechos reservados.</p>
    </div>

</body>
</html>
