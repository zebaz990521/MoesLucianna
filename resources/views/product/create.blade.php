<x-app-layout>

    <x-slot:title>
        Nuevo Producto
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

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Crear Nuevo Producto</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
                                <li class="breadcrumb-item active">Productos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

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

                            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Producto</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio</label>
                                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required step="0.01">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Cantidad</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoría</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="status" id="estado" class="form-select">
                                        <option value="available">Disponible</option>
                                        {{-- <option value="vendido">Vendido</option> --}}
                                        <option value="archived">Archivado</option>
                                        <option value="out_of_stock">Fuera de Stock</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">Imágenes</label>
                                    <input type="file" name="images[]" id="images" class="form-control" multiple>
                                    <div id="imagePreview" class="mt-3"></div>
                                </div>

                                <button type="submit" class="btn btn-success">Guardar Producto</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
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
        <script>
            document.getElementById('images').addEventListener('change', function(event) {
                let preview = document.getElementById('imagePreview');
                preview.innerHTML = '';
                Array.from(event.target.files).forEach(file => {
                    let img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-thumbnail me-2';
                    img.width = 100;
                    preview.appendChild(img);
                });
            });
        </script>
    </x-slot>

</x-app-layout>
