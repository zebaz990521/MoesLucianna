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
                                    <label for="productSelect2" class="form-label">Buscar Producto</label>
                                    <select id="productSelect2" class="form-control" style="width: 100%;">
                                        <option value="">Seleccione un producto...</option>
                                    </select>
                                </div>

                                <div id="selectedProduct" class="d-none">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0 text-white">Producto Seleccionado</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <img id="productImage" src="" class="img-thumbnail me-3" width="80" height="80" style="object-fit: cover;">
                                                <div>
                                                    <strong id="productName"></strong>
                                                    <div id="productStock" class="text-muted small"></div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="unit_cost" class="form-label">Costo Unitario</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" id="unit_cost" class="form-control" min="0.01" step="0.01" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="quantity" class="form-label">Cantidad</label>
                                                    <input type="number" id="quantity" class="form-control" min="1" placeholder="1">
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-primary mt-3 w-100" id="add-product">
                                                <i class="fas fa-plus me-2"></i>Agregar Producto
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="table-dark">
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
                                            <tr id="empty-row" class="text-center text-muted">
                                                <td colspan="6">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i>
                                                    No hay productos agregados
                                                </td>
                                            </tr>
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

        $(document).ready(function () {
            // Inicializar Select2 para proveedor y tipo de documento
            $('#supplier_id').select2({
                width: '100%',
                language: 'es',
                placeholder: 'Seleccione un proveedor...'
            });

            $('#document_type_id').select2({
                width: '100%',
                language: 'es',
                placeholder: 'Seleccione un tipo de documento...'
            });

            // Inicializar Select2 para productos con AJAX
            $('#productSelect2').select2({
                width: '100%',
                language: 'es',
                placeholder: 'Buscar producto por nombre...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('products.search') }}",
                    type: 'POST',
                    dataType: 'json',
                    delay: 300,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.map(function(product) {
                                return {
                                    id: product.id,
                                    text: product.name,
                                    price: product.price,
                                    image: product.image,
                                    stock: product.stock || 0,
                                    category: product.category || ''
                                };
                            }),
                            pagination: {
                                more: false
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatProductOption,
                templateSelection: formatProductSelection,
                escapeMarkup: function (markup) { return markup; }
            });

            // Evento cuando se selecciona un producto
            $('#productSelect2').on('select2:select', function (e) {
                let data = e.params.data;
                selectProduct(data.id, data.text, data.image, data.price, data.stock, data.category);
            });

            // Evento del botón agregar producto
            $('#add-product').on('click', function() {
                addProductToPurchase();
            });
        });

        // Función para formatear las opciones en el dropdown
        function formatProductOption(product) {
            if (product.loading) {
                return product.text;
            }

            let $container = $(
                '<div class="d-flex align-items-center">' +
                    '<img class="me-3 rounded" style="width: 40px; height: 30px; object-fit: cover;" />' +
                    '<div>' +
                        '<div class="fw-bold"></div>' +
                        '<small class="text-muted"></small>' +
                    '</div>' +
                '</div>'
            );

            $container.find('img').attr('src', product.image);
            $container.find('.fw-bold').text(product.text);
            $container.find('small').text(`Precio: $${product.price} | Stock: ${product.stock}`);

            return $container;
        }

        // Función para formatear la selección
        function formatProductSelection(product) {
            if (!product.id) {
                return product.text;
            }
            return product.text;
        }

        // Función para seleccionar producto
        function selectProduct(id, name, image, price, stock, category) {
            $('#selectedProduct').removeClass('d-none');
            $('#productName').text(name);
            $('#productImage').attr('src', image);
            $('#unit_cost').val(price);
            $('#add-product').data('id', id)
                            .data('name', name)
                            .data('image', image)
                            .data('stock', stock)
                            .data('category', category);

            // Mostrar información adicional del stock y categoría
            let stockInfo = `Stock: ${stock}`;
            if (category) {
                stockInfo += ` | Categoría: ${category}`;
            }
            $('#productStock').text(stockInfo);
        }

        // Función para agregar producto a la compra
        function addProductToPurchase() {
            let $btn = $('#add-product');
            let productId = $btn.data('id');
            let productName = $btn.data('name');
            let productImage = $btn.data('image');
            let productStock = $btn.data('stock') || 0;
            let productCategory = $btn.data('category') || '';
            let unitPrice = parseFloat($('#unit_cost').val());
            let quantity = parseInt($('#quantity').val());

            // Validaciones mejoradas
            if (!unitPrice || unitPrice <= 0) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "El costo unitario debe ser mayor a 0.",
                });
                return;
            }

            if (!quantity || quantity < 1) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "La cantidad debe ser mayor a 0.",
                });
                return;
            }

            // Verificar si el producto ya existe en la compra
            let existingProductIndex = purchaseDetails.findIndex(item => item.product_id == productId);
            if (existingProductIndex !== -1) {
                Swal.fire({
                    icon: "question",
                    title: "Producto ya existe",
                    text: "Este producto ya está en la compra. ¿Desea actualizar la cantidad?",
                    showCancelButton: true,
                    confirmButtonText: "Sí, actualizar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        purchaseDetails[existingProductIndex].quantity += quantity;
                        purchaseDetails[existingProductIndex].unit_cost = unitPrice;
                        purchaseDetails[existingProductIndex].subtotal = purchaseDetails[existingProductIndex].quantity * unitPrice;
                        updateTable();
                    }
                });
                return;
            }

            let subtotal = unitPrice * quantity;
            purchaseDetails.push({
                product_id: productId,
                name: productName,
                image: productImage,
                quantity: quantity,
                unit_cost: unitPrice,
                subtotal: subtotal,
                stock: productStock,
                category: productCategory
            });

            $('#details').val(JSON.stringify(purchaseDetails));
            updateTable();
            resetProductSelection();
        }

        // Función para resetear la selección de producto
        function resetProductSelection() {
            $('#selectedProduct').addClass('d-none');
            $('#productSelect2').val(null).trigger('change');
            $('#quantity').val('');
            $('#unit_cost').val('');
        }

        // Función mejorada para actualizar la tabla
        function updateTable() {
            let tableBody = $('#purchase-details');
            let emptyRow = $('#empty-row');
            tableBody.empty();
            let totalCost = 0;

            if (purchaseDetails.length === 0) {
                tableBody.append(emptyRow);
                $('#total_cost').val('0.00');
                return;
            }

            purchaseDetails.forEach((item, index) => {
                totalCost += item.subtotal;

                let $tr = $('<tr/>');
                let $tdImg = $('<td/>').append(
                    $('<img/>', {
                        src: item.image,
                        class: 'img-thumbnail',
                        width: '50px',
                        height: '50px',
                        style: 'object-fit: cover; width: 80px; height: 60px;'
                    })
                );
                let $tdName = $('<td/>').html(`
                    <div>
                        <strong>${item.name}</strong>
                        ${item.category ? `<br><small class="text-muted">${item.category}</small>` : ''}
                        ${item.stock !== undefined ? `<br><small class="text-info">Stock: ${item.stock}</small>` : ''}
                    </div>
                `);
                let $tdQty = $('<td/>').html(`
                    <input type="number" class="form-control form-control-sm"
                           value="${item.quantity}" min="1"
                           onchange="updateQuantity(${index}, this.value)">
                `);
                let $tdPrice = $('<td/>').html(`
                    <input type="number" class="form-control form-control-sm"
                           value="${item.unit_cost}" min="0.01" step="0.01"
                           onchange="updatePrice(${index}, this.value)">
                `);
                let $tdSubtotal = $('<td/>').html(`<strong>$${item.subtotal.toFixed(2)}</strong>`);
                let $actions = $('<td/>').append(
                    $('<button/>', {
                        class: 'btn btn-danger btn-sm',
                        text: 'Eliminar',
                        click: function(e){
                            e.preventDefault();
                            removeProduct(index);
                        }
                    })
                );

                $tr.append($tdImg, $tdName, $tdQty, $tdPrice, $tdSubtotal, $actions);
                tableBody.append($tr);
            });

            $('#total_cost').val(totalCost.toFixed(2));
        }

        // Funciones para actualizar cantidad y precio en tiempo real
        function updateQuantity(index, newQuantity) {
            let quantity = parseInt(newQuantity);
            if (quantity < 1) {
                purchaseDetails[index].quantity = 1;
                quantity = 1;
            }

            purchaseDetails[index].quantity = quantity;
            purchaseDetails[index].subtotal = quantity * purchaseDetails[index].unit_cost;

            $('#details').val(JSON.stringify(purchaseDetails));
            updateTable();
        }

        function updatePrice(index, newPrice) {
            let price = parseFloat(newPrice);
            if (price <= 0) {
                purchaseDetails[index].unit_cost = 0.01;
                price = 0.01;
            }

            purchaseDetails[index].unit_cost = price;
            purchaseDetails[index].subtotal = purchaseDetails[index].quantity * price;

            $('#details').val(JSON.stringify(purchaseDetails));
            updateTable();
        }

        // Función mejorada para eliminar producto
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
        </script>
    </x-slot>

</x-app-layout>
