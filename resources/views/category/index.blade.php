<x-app-layout>

      <x-slot:title>
          Mantenimiento de Categorias
      </x-slot>



  <!-- ========== Header ========== -->
  <x-slot:header>
      @include('partials.header')
  </x-slot>

  <!-- ========== App Menu ========== -->
  <x-slot:menu>
      @include('partials.menu')
  </x-slot>

              <!-- ============================================================== -->
              <!-- Content Page -->
              <!-- ============================================================== -->
              <!-- Left Sidebar End -->
              <!-- Vertical Overlay-->
              <div class="vertical-overlay"></div>

              <!-- ============================================================== -->
              <!-- Start right Content here -->
              <!-- ============================================================== -->
              {{-- <div class="main-content"> --}}

                  <div class="page-content">
                      <div class="container-fluid">

                          <!-- start page title -->
                          <div class="row">
                              <div class="col-12">
                                  <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                      <h4 class="mb-sm-0">Listado Categorias</h4>

                                      <div class="page-title-right">
                                          <ol class="breadcrumb m-0">
                                              <li class="breadcrumb-item"><a href="javascript: void(0);">Mantenimiento</a></li>
                                              <li class="breadcrumb-item active">Categoria</li>
                                          </ol>
                                      </div>



                                  </div>
                              </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-12">
                                <div class="card">

                                    <div class="card-body p-3">

                                        @if (session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif

                                        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Agregar Categoría</a>

                                        <div class="table-responsive">
                                            <table id="categoriesTable" class="table table-striped table-bordered w-100">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($categories as $category)
                                                        <tr>
                                                            <td>{{ $category->id }}</td>
                                                            <td>{{ $category->name }}</td>
                                                            <td>{{ $category->description ?? 'Sin descripción' }}</td>
                                                            <td>
                                                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">Editar</a>
                                                                <button class="btn btn-danger btn-sm delete-category" data-id="{{ $category->id }}">Eliminar</button>
                                                                <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" style="display: none;">
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
                          <!-- end page title -->

                      </div>
                      <!-- container-fluid -->
                  </div>
                  <!-- End Page-content -->

                  <x-slot:footer>
                      @include('partials.footer')
                  </x-slot>

           {{--    </div> --}}
              <!-- end main content-->

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
            $('#categoriesTable').DataTable({
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

            // Función para eliminar con SweetAlert2
            $(document).on('click', '.delete-category', function() {
                let categoryId = $(this).data('id');
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
                        document.getElementById('delete-form-' + categoryId).submit();
                    }
                });
            });
        });
    </script>
  </x-slot>
  </x-app-layout>







