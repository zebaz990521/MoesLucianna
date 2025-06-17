<x-app-layout>

    <x-slot:title>
        Mantenimiento de Usuarios
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
                        <h4 class="mb-sm-0">Listado de Usuarios</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                <li class="breadcrumb-item active">Usuarios</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-3">

                            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Agregar Usuario</a>

                            <div class="table-responsive">
                                <table id="usersTable" class="table table-striped table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Verificación</th>
                                            <th>Teléfono</th>
                                            <th>Dirección</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>
                                                    @if ($user->photo)
                                                        <img src="{{ asset('storage/' . $user->photo) }}" class="rounded-circle" width="50" height="50" alt="Foto">
                                                    @else
                                                        <img src="{{ asset('assets/images/users/default.png') }}" class="rounded-circle" width="50" height="50" alt="Foto por defecto">
                                                    @endif
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $user->email_verified_at ? 'success' : 'danger' }}">
                                                        {{ $user->email_verified_at ? 'Verificado' : 'No verificado' }}
                                                    </span>
                                                </td>
                                                <td>{{ $user->phone ?? 'No especificado' }}</td>
                                                <td>{{ $user->address ?? 'No especificado' }}</td>
                                                <td>{{ $user->role->name }}</td>
                                                <td>
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Editar</a>
                                                    <button class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}">Eliminar</button>
                                                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" style="display: none;">
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
                $('#usersTable').DataTable({
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

                $(document).on('click', '.delete-user', function() {
                    let userId = $(this).data('id');
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
                                Swal.fire({
                                    title: "Eliminado!",
                                    text: "Registro Eliminado Exitosamente.",
                                    icon: "success"
                                }).then((result) => {
                                    document.getElementById('delete-form-' + userId).submit();
                                })
                        }
                    });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
