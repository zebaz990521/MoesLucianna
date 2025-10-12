<x-app-layout>

    <x-slot:title>Mantenimiento de Compras</x-slot:title>

    <x-slot:header>@include('partials.header')</x-slot:header>
    <x-slot:menu>@include('partials.menu')</x-slot:menu>

    <div class="vertical-overlay"></div>

    <div class="page-content">
        <div class="container-fluid">

            <!-- 🔹 Encabezado -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"><i class="ri-shopping-cart-2-line me-2"></i>Listado de Compras</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                <li class="breadcrumb-item active">Compras</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


                     <!-- 🔹 Mensajes -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="ri-check-double-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- 🔹 Contenido principal -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ri-file-list-3-line me-1"></i>Gestión de Compras</h5>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    <i class="ri-add-circle-line me-1"></i>Agregar Compra
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchasesTable" class="table table-hover table-striped align-middle border rounded">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Proveedor</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-center">Archivos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td><strong>#{{ $purchase->id }}</strong></td>
                                    <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->user->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_datetime)->format('d/m/Y H:i') }}</td>
                                    <td><strong>${{ number_format($purchase->total_cost, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$purchase->status] ?? 'secondary' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if($purchase->purchase_invoice)
                                                <a href="{{ route('purchases.viewFile', ['purchase' => $purchase->id, 'type' => 'invoice']) }}"
                                                   class="btn btn-sm btn-info" target="_blank" data-bs-toggle="tooltip" title="Ver factura">
                                                    <i class="ri-file-text-line"></i>
                                                </a>
                                                <a href="{{ route('purchases.downloadFile', ['purchase' => $purchase->id, 'type' => 'invoice']) }}"
                                                   class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Descargar factura">
                                                    <i class="ri-download-2-line"></i>
                                                </a>
                                            @endif

                                            @if($purchase->pdf_url)
                                                <a href="{{ route('purchases.viewFile', ['purchase' => $purchase->id, 'type' => 'pdf']) }}"
                                                   class="btn btn-sm btn-success" target="_blank" data-bs-toggle="tooltip" title="Ver PDF">
                                                    <i class="ri-file-pdf-line"></i>
                                                </a>
                                                <a href="{{ route('purchases.downloadFile', ['purchase' => $purchase->id, 'type' => 'pdf']) }}"
                                                   class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Descargar PDF">
                                                    <i class="ri-download-2-line"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#purchaseModal{{ $purchase->id }}"
                                                data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @if($purchase->status !== 'cancelled')
                                                <form action="{{ route('purchases.cancel', $purchase->id) }}" method="POST" class="cancel-form d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Cancelar compra">
                                                        <i class="ri-close-circle-line"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>


                </div>

                @foreach ($purchases as $purchase)
                                <!-- 🔹 Modal Detalles -->
                                <div class="modal fade" id="purchaseModal{{ $purchase->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $purchase->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content shadow-lg border-0">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="ri-information-line me-2"></i>Detalles de Compra #{{ $purchase->id }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted">Proveedor</h6>
                                                        <p><strong>{{ $purchase->supplier->name ?? 'N/A' }}</strong></p>
                                                        <p>Email: {{ $purchase->supplier->email ?? 'N/A' }}</p>
                                                        <p>Teléfono: {{ $purchase->supplier->phone ?? 'N/A' }}</p>
                                                        <p>Dirección: {{ $purchase->supplier->address ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted">Información</h6>
                                                        <p>Usuario: {{ $purchase->user->name ?? 'N/A' }}</p>
                                                        <p>Documento: {{ $purchase->documentType->name ?? 'N/A' }}</p>
                                                        <p>Fecha: {{ \Carbon\Carbon::parse($purchase->purchase_datetime)->format('d/m/Y H:i') }}</p>
                                                        <p>Estado:
                                                            <span class="badge bg-{{ $statusColors[$purchase->status] ?? 'secondary' }}">
                                                                {{ ucfirst($purchase->status) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered align-middle">
                                                        <thead class="table-light">
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
                                                            @foreach ($purchase->purchaseDetails as $index => $detail)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                                                                    <td>{{ $detail->product->category->name ?? 'N/A' }}</td>
                                                                    <td>{{ $detail->quantity }}</td>
                                                                    <td>${{ number_format($detail->unit_cost, 2) }}</td>
                                                                    <td><strong>${{ number_format($detail->subtotal, 2) }}</strong></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="text-end mt-3">
                                                    <h5 class="fw-bold text-primary">Total: ${{ number_format($purchase->total_cost, 2) }}</h5>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="ri-close-line"></i> Cerrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                @endforeach
            </div>
        </div>



        </div>
    </div>

    <x-slot:footer>@include('partials.footer')</x-slot>
    <x-slot:start>@include('partials.start-back-to-top')</x-slot>
    <x-slot:theme>@include('partials.theme')</x-slot>

    <x-slot:js>
        @include('partials.js')

        <script>
            $(document).ready(function() {
                $('#purchasesTable').DataTable({
                    language:  {url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'},
                    order: [[0, 'desc']],
                    responsive: true,
                    columnDefs: [{ orderable: false, targets: [6, 7] }]
                });

                // Activar tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();

                // SweetAlert2 Cancelar compra
                $('.cancel-form').on('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    Swal.fire({
                        title: "¿Cancelar esta compra?",
                        text: "Esta acción revertirá el stock asociado.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, cancelar",
                        cancelButtonText: "No, mantener"
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
