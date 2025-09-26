{{-- <x-app-layout>

    <x-slot:title>
        Nueva Compra
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
                        <h4 class="mb-sm-0">Registrar Nueva Compra</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Compras</a></li>
                                <li class="breadcrumb-item active">Nueva Compra</li>
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

                            <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Proveedor</label>
                                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="document_type_id" class="form-label">Tipo de Documento</label>
                                    <select name="document_type_id" id="document_type_id" class="form-select" required>
                                        @foreach ($documentTypes as $documentType)
                                            <option value="{{ $documentType->id }}">{{ $documentType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_datetime" class="form-label">Fecha de Compra</label>
                                    <input type="datetime-local" name="purchase_datetime" id="purchase_datetime" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_invoice" class="form-label">Factura (Opcional)</label>
                                    <input type="file" name="purchase_invoice" id="purchase_invoice" class="form-control">
                                </div>

                                <hr>

                                <h5>Detalles de la Compra</h5>

                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Producto</label>
                                    <select id="product_id" class="form-select">
                                        <option value="">Seleccione un producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Cantidad</label>
                                    <input type="number" id="quantity" class="form-control" min="1">
                                </div>

                                <button type="button" class="btn btn-primary mb-3" id="add-product">Agregar Producto</button>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="purchase-details">
                                        </tbody>
                                    </table>
                                </div>

                                <input type="hidden" name="details" id="details">

                                <div class="mb-3">
                                    <label class="form-label">Costo Total</label>
                                    <input type="text" id="total_cost" class="form-control" readonly value="0">
                                </div>

                                <button type="submit" class="btn btn-success">Guardar Compra</button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
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
            let purchaseDetails = [];

            document.getElementById('add-product').addEventListener('click', function() {
                let productSelect = document.getElementById('product_id');
                let quantityInput = document.getElementById('quantity');

                let productId = productSelect.value;
                let productName = productSelect.options[productSelect.selectedIndex].text;
                let unitPrice = parseFloat(productSelect.options[productSelect.selectedIndex].getAttribute('data-price'));
                let quantity = parseInt(quantityInput.value);

                if (!productId || quantity < 1) {
                    alert('Seleccione un producto y una cantidad válida.');
                    return;
                }

                let subtotal = unitPrice * quantity;
                purchaseDetails.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_cost: unitPrice,
                    subtotal: subtotal
                });

                document.getElementById('details').value = JSON.stringify(purchaseDetails);
                updateTable();
            });

            function updateTable() {
                let tableBody = document.getElementById('purchase-details');
                tableBody.innerHTML = "";
                let totalCost = 0;

                purchaseDetails.forEach((item, index) => {
                    totalCost += item.subtotal;
                    tableBody.innerHTML += `
                        <tr>
                            <td>${item.product_id}</td>
                            <td>${item.quantity}</td>
                            <td>$${item.unit_cost.toFixed(2)}</td>
                            <td>$${item.subtotal.toFixed(2)}</td>
                            <td><button class="btn btn-danger btn-sm" onclick="removeProduct(${index})">Eliminar</button></td>
                        </tr>
                    `;
                });

                document.getElementById('total_cost').value = totalCost.toFixed(2);
            }

            function removeProduct(index) {
                purchaseDetails.splice(index, 1);
                document.getElementById('details').value = JSON.stringify(purchaseDetails);
                updateTable();
            }
        </script>
    </x-slot>

</x-app-layout>
 --}}


 <x-app-layout>

    <x-slot:title>
        Nueva Compra
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
                        <h4 class="mb-sm-0">Registrar Nueva Compra</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Compras</a></li>
                                <li class="breadcrumb-item active">Nueva Compra</li>
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

                            <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Proveedor</label>
                                    <select  name="supplier_id" id="supplier_id"  class="js-example-basic-single" required>
                                        <option value="" disabled selected>Seleccione un proveedor...</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="document_type_id" class="form-label">Tipo de Documento</label>
                                    <select name="document_type_id" id="document_type_id" class="js-example-basic-single" >
                                        <option value="" disabled selected>Seleccione un tipo de factura de documento...</option>
                                        @foreach ($documentTypes as $documentType)
                                            <option value="{{ $documentType->id }}">{{ $documentType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_datetime" class="form-label">Fecha de Compra</label>
                                    <input type="datetime-local" name="purchase_datetime" id="purchase_datetime" class="form-control" >
                                </div>

                                <div class="mb-3">
                                    <label for="purchase_invoice" class="form-label">Factura (Opcional)</label>
                                    <input type="file" name="purchase_invoice" id="purchase_invoice" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado de la Compra</label>
                                    <select name="status" id="estado" class="form-select">
                                        <option value="pending">Pendiente</option>
                                        <option value="completed">Completada</option>
                                        <option value="cancelled">Cancelada</option>
                                    </select>
                                </div>

                                <hr>

                                <h5>Detalles de la Compra</h5>

                                <div class="mb-3">
                                    <label for="searchProduct" class="form-label">Buscar Producto</label>
                                     <input type="text" id="searchProduct" class="form-control" placeholder="Buscar producto...">
                                    <div id="productResults" class="list-group mt-2"></div>
                                </div>
                                <div id="selectedProduct" class="d-none">


                                    <div class="mb-3">
                                        <label class="form-label">Producto Seleccionado</label>
                                        <div class="d-flex align-items-center">
                                            <img id="productImage" src="" class="img-thumbnail me-3" width="80">
                                            <strong id="productName"></strong>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="unit_cost" class="form-label">Costo Unitario</label>
                                        <input type="number" id="unit_cost" class="form-control" min="0.01" step="0.01">
                                    </div>

                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Cantidad</label>
                                        <input type="number" id="quantity" class="form-control" min="1">
                                    </div>

                                    <button type="button" class="btn btn-primary mb-3" id="add-product">Agregar Producto</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Imagen</th>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="purchase-details">
                                        </tbody>
                                    </table>
                                </div>


                                <input type="hidden" name="details" id="details">

                                <div class="mb-3">
                                    <label class="form-label">Costo Total</label>
                                    <input type="text" id="total_cost" class="form-control" readonly value="0">
                                </div>

                                <button type="submit" class="btn btn-success">Guardar Compra</button>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
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
          let purchaseDetails = [];

            $('#searchProduct').on('input', function() {
                let query = $(this).val().trim();
                let results = $('#productResults');
                if (query.length > 2) {
                    $.ajax({
                        url: "{{ route('products.search') }}",
                        type: "POST",
                        data: { search: query },
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {

                           /*  results.empty()
                            response.forEach(product => {
                                results.append(`
                                <a  class="list-group-item list-group-item-action" onclick="selectProduct(${product.id}, '${product.name}', '${product.image}', ${product.price})">
                                    <div class="d-flex gap-2 justify-content-start align-item-center">
                                        <img src="${product.image}" alt="imagen" width="40" height="30">
                                        <p class="m-0">
                                            ${product.name}
                                        </p>
                                    </div
                                </a>`);
                            }); */

                    results.empty();
                    if (!Array.isArray(response) || response.length === 0) {
                        // opcional: mostrar "no results"
                        // results.append('<div class="list-group-item">No se encontraron productos</div>');
                        return;
                    }

                    response.forEach(function(product) {
                        // crear elementos con jQuery en lugar de interpolar strings (evita problemas con comillas)
                        let $a = $('<a/>', {
                            href: '#',
                            'class': 'list-group-item list-group-item-action product-result'
                        });

                        let $inner = $('<div/>', { 'class': 'd-flex gap-2 align-items-center' });
                        let $img = $('<img/>', {
                            src: product.image,
                            alt: 'imagen',
                            width: 40,
                            height: 30,
                            'class': 'rounded'
                        });
                        let $p = $('<p/>', { 'class': 'm-0 ms-2' }).text(product.name);

                        $inner.append($img).append($p);
                        $a.append($inner);

                        // almacenar datos seguros usando .data()
                        $a.data('id', product.id);
                        $a.data('name', product.name);
                        $a.data('image', product.image);
                        $a.data('price', product.price);

                        results.append($a);
                        });
                    },
                    error: function(xhr, status, err) {
                        // en caso de error limpiar resultados
                        results.empty();
                        console.error('Error en búsqueda de productos:', err);
                }
                });
                } else {
                    /* let results = $('#productResults'); */
                    results.empty()
                }
            });

            // Delegación de evento: seleccionar producto desde la lista
            $(document).on('click', '.product-result', function(e) {
                e.preventDefault();
                let $el = $(this);
                let id = $el.data('id');
                let name = $el.data('name');
                let image = $el.data('image');
                let price = $el.data('price');

                selectProduct(id, name, image, price);
            });

            function selectProduct(id, name, image, price) {
                $('#productResults').empty();
                $('#selectedProduct').removeClass('d-none');
                $('#productName').text(name);
                $('#productImage').attr('src', image);
                $('#unit_cost').val(price);
                $('#add-product').data('id', id).data('name', name).data('image', image);
            }

            $('#add-product').on('click', function() {
                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let productImage = $(this).data('image');
                let unitPrice = parseFloat($('#unit_cost').val());
                let quantity = parseInt($('#quantity').val());
                let subtotal = unitPrice * quantity;

                if (!unitPrice || unitPrice <= 0) {
                    Swal.fire("Error", "El costo unitario debe ser mayor a 0.", "error");
                    return;
                }

                if (!quantity || quantity < 1) {
                    Swal.fire("Error", "La cantidad debe ser mayor a 0.", "error");
                    return;
                }

                purchaseDetails.push({ product_id: productId, name: productName, image: productImage, quantity: quantity, unit_cost: unitPrice, subtotal: subtotal });

                $('#details').val(JSON.stringify(purchaseDetails));
                updateTable();
                $('#selectedProduct').addClass('d-none');
                $('#searchProduct').val('');
            });

            function updateTable() {
                let tableBody = $('#purchase-details');
                tableBody.empty();
                let totalCost = 0;

                purchaseDetails.forEach((item, index) => {
                    totalCost += item.subtotal;
                    /* tableBody.append(`
                        <tr>
                            <td><img src="${item.image}" class="img-thumbnail" width="80"></td>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>$${item.unit_cost.toFixed(2)}</td>
                            <td>$${item.subtotal.toFixed(2)}</td>
                            <td>
                                <a class="btn btn-warning btn-sm" onclick="editProduct(${index})">Editar</a>
                                <a class="btn btn-danger btn-sm" onclick="removeProduct(${index})">Eliminar</a>
                            </td>
                        </tr>
                    `); */
                     // construir fila seguro con jQuery
                    let $tr = $('<tr/>');
                    let $tdImg = $('<td/>').append($('<img/>', { src: item.image, class: 'img-thumbnail', width: 80 }));
                    let $tdName = $('<td/>').text(item.name);
                    let $tdQty = $('<td/>').text(item.quantity);
                    let $tdPrice = $('<td/>').text(`$${item.unit_cost.toFixed(2)}`);
                    let $tdSubtotal = $('<td/>').text(`$${item.subtotal.toFixed(2)}`);
                    let $actions = $('<td/>').append(
                        $('<a/>', { href: '#', class: 'btn btn-warning btn-sm me-1', text: 'Editar', click: function(e){ e.preventDefault(); editProduct(index); } }),
                        $('<a/>', { href: '#', class: 'btn btn-danger btn-sm', text: 'Eliminar', click: function(e){ e.preventDefault(); removeProduct(index); } })
                    );

                    $tr.append($tdImg, $tdName, $tdQty, $tdPrice, $tdSubtotal, $actions);
                    tableBody.append($tr);
                });

                $('#total_cost').val(totalCost.toFixed(2));
            }

            function removeProduct(index) {
                Swal.fire({
                    title: "¿Eliminar este producto?",
                    text: "Esta acción no se puede deshacer",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        purchaseDetails.splice(index, 1);
                        $('#details').val(JSON.stringify(purchaseDetails));
                        updateTable();
                        Swal.fire("Eliminado", "El producto ha sido eliminado.", "success");
                    }
                });
            }

        function editProduct(index) {
            let product = purchaseDetails[index];

            Swal.fire({
                title: "Editar Producto",
                html: `
                    <img src="${product.image}" class="img-thumbnail mb-3" width="100">
                    <h3 class="mb-2">${product.name}</h3>
                    <input type="number" id="editQuantity" class="form-control mb-2" min="1" value="${product.quantity}">
                    <input type="number" id="editUnitCost" class="form-control" min="0.01" step="0.01" value="${product.unit_cost}">
                `,
                showCancelButton: true,
                confirmButtonText: "Actualizar",
                cancelButtonText: "Cancelar",
                preConfirm: () => {
                    let newQuantity = parseInt(document.getElementById('editQuantity').value);
                    let newUnitCost = parseFloat(document.getElementById('editUnitCost').value);

                    if (newQuantity < 1 || newUnitCost <= 0) {
                        Swal.showValidationMessage("La cantidad debe ser mayor a 0 y el costo unitario debe ser mayor a 0.");
                        return false;
                    }

                    return { newQuantity, newUnitCost };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    purchaseDetails[index].quantity = result.value.newQuantity;
                    purchaseDetails[index].unit_cost = result.value.newUnitCost;
                    purchaseDetails[index].subtotal = result.value.newQuantity * result.value.newUnitCost;

                    $('#details').val(JSON.stringify(purchaseDetails));
                    updateTable();
                    Swal.fire("Actualizado", "El producto ha sido actualizado.", "success");
                }
            });

            $('#supplier_id').select2({
                width: '100%',
                placeholder: 'Seleccione un proveedor...'
            })
            $('#document_type_id').select2({
                width: '100%',
                placeholder: 'Seleccione un tipo de documento...'
            })
        }
        </script>
    </x-slot>

</x-app-layout>
