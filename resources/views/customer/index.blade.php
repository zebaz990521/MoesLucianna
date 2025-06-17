<x-app-layout>

    <x-slot:title>
        Mantenimiento de Clientes
    </x-slot>

    <x-slot:header>
        @include('partials.header')
    </x-slot>

    <x-slot:menu>
        @include('partials.menu')
    </x-slot>

    <div class="vertical-overlay"></div>

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Listado de Clientes</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                <li class="breadcrumb-item active">Clientes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-3">

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Agregar Cliente</a>

                            <div class="table-responsive">
                                <table id="customersTable" class="table table-striped table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nit</th>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Dirección</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->nit }}</td>
                                                <td>
                                                    @if ($customer->photo)
                                                        <img src="{{ asset('storage/' . $customer->photo) }}" class="rounded-circle" width="50" height="50" alt="Foto">
                                                    @else
                                                        <img src="{{ asset('assets/images/users/default.png') }}" class="rounded-circle" width="50" height="50" alt="Foto por defecto">
                                                    @endif
                                                </td>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->phone ?? 'No especificado' }}</td>
                                                <td>{{ $customer->address ?? 'No especificado' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($customer->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">Editar</a>
                                                    <button class="btn btn-danger btn-sm delete-customer" data-id="{{ $customer->id }}">Eliminar</button>
                                                    <form id="delete-form-{{ $customer->id }}" action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-slot:footer>
        @include('partials.footer')
    </x-slot>

    <x-slot:start>
        @include('partials.start-back-to-top')
    </x-slot>

    <x-slot:theme>
        @include('partials.theme')
    </x-slot>

    <x-slot:js>
        @include('partials.js')
        <script>
            $(document).ready(function() {
                $('#customersTable').DataTable({
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron resultados",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(filtrado de _MAX_ registros en total)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        }
                    },
                    "order": [[0, "asc"]]
                });

                $(document).on('click', '.delete-customer', function() {
                    let customerId = $(this).data('id');
                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Esta acción no se puede deshacer",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + customerId).submit();
                        }
                    });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
