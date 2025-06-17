<x-app-layout>

    <x-slot:title>
        Nuevo Proveedor
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
                        <h4 class="mb-sm-0">Crear Nuevo Proveedor</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                <li class="breadcrumb-item active">Proveedores</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body p-4">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="rut" class="form-label">RUT</label>
                                    <input type="text" name="rut" id="rut" class="form-control" value="{{ old('rut') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Proveedor</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Dirección</label>
                                    <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto</label>
                                    <input type="file" name="photo" id="photo" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="status" id="estado" class="form-select">
                                        <option value="active" selected>Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Guardar Proveedor</button>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a>
                            </form>

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
    </x-slot>

</x-app-layout>