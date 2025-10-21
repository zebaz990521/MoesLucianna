-- ============================================================
-- MODELO ENTIDAD-RELACIÓN (EER): SISTEMA POS LICORERA MOES LUCIANNA
-- ============================================================
-- Script completo con todas las tablas para un sistema POS robusto
-- Incluye: envases retornables, lotes con vencimiento,
-- pagos mixtos, devoluciones/reembolsos, créditos y caja
-- ============================================================
-- Versión: 1.0
-- Fecha: 2025-10-18
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;

-- ============================================================
-- 1. TABLAS BÁSICAS DE USUARIOS Y SEGURIDAD
-- ============================================================

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles de usuarios del sistema';

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    photo VARCHAR(255),
    role_id BIGINT UNSIGNED NOT NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    KEY idx_email (email),
    KEY idx_role_id (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema (vendedores, gerentes, admin)';

-- ============================================================
-- 2. TABLAS DE CATÁLOGOS Y REFERENCIAS
-- ============================================================

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías de productos (Cervezas, Mecatos, Comestibles, etc)';

CREATE TABLE IF NOT EXISTS suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rut VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    photo VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_rut (rut),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proveedores/Distribuidores de productos';

CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(20),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    photo VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_nit (nit),
    KEY idx_status (status),
    KEY idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes que compran en la licorera';

CREATE TABLE IF NOT EXISTS document_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    code VARCHAR(10),
    description TEXT,
    type ENUM('purchase', 'sale') DEFAULT 'sale',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tipos de documentos (Factura, Boleta, Nota Crédito, etc)';

-- ============================================================
-- 3. TABLAS DE PRODUCTOS Y ENVASES
-- ============================================================

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    sku VARCHAR(100) UNIQUE,
    barcode VARCHAR(100) UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    cost_price DECIMAL(10, 2) DEFAULT 0,
    wholesale_price DECIMAL(10, 2),
    quantity INTEGER NOT NULL DEFAULT 0,
    min_stock INTEGER DEFAULT 0,
    max_stock INTEGER DEFAULT 0,
    has_expiration BOOLEAN DEFAULT FALSE COMMENT 'Indica si el producto tiene fecha de vencimiento',
    expiration_alert_days INTEGER DEFAULT 7 COMMENT 'Días antes de vencer para mostrar alerta',
    has_container BOOLEAN DEFAULT FALSE COMMENT 'Indica si el producto es retornable (tiene envase)',
    alcohol_percentage DECIMAL(5, 2) COMMENT 'Porcentaje de alcohol (para bebidas alcohólicas)',
    volume_ml INTEGER COMMENT 'Volumen en mililitros',
    requires_id BOOLEAN DEFAULT FALSE COMMENT 'Requiere verificación de cédula',
    is_combo BOOLEAN DEFAULT FALSE COMMENT 'Indica si es un combo de productos',
    brand VARCHAR(255),
    status ENUM('available', 'out_of_stock', 'archived') DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    KEY idx_sku (sku),
    KEY idx_barcode (barcode),
    KEY idx_status (status),
    KEY idx_has_expiration (has_expiration),
    KEY idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Productos (Cervezas, Mecatos, Comestibles con/sin vencimiento)';

CREATE TABLE IF NOT EXISTS product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Imágenes asociadas a cada producto';

CREATE TABLE IF NOT EXISTS containers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    deposit_price DECIMAL(10, 2) NOT NULL COMMENT 'Precio del depósito/retorno',
    type ENUM('bottle', 'case', 'keg') NOT NULL COMMENT 'Tipo de envase: botella, caja, barril',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_type (type),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de envases retornables (botellas, cajas, barriles)';

CREATE TABLE IF NOT EXISTS product_containers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    container_id BIGINT UNSIGNED NOT NULL,
    quantity INTEGER DEFAULT 1 COMMENT 'Cantidad de envases por producto (ej: 1 caja = 24 botellas)',
    is_returnable BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (container_id) REFERENCES containers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_container (product_id, container_id),
    KEY idx_product_id (product_id),
    KEY idx_container_id (container_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación entre productos y sus envases retornables';

CREATE TABLE IF NOT EXISTS container_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    container_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    sale_id BIGINT UNSIGNED,
    type ENUM('out', 'return') NOT NULL COMMENT 'out=venta de envase, return=devolución de envase',
    quantity INTEGER NOT NULL,
    deposit_amount DECIMAL(10, 2) COMMENT 'Monto de depósito cobrado o devuelto',
    user_id BIGINT UNSIGNED NOT NULL,
    movement_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (container_id) REFERENCES containers(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_type (type),
    KEY idx_movement_datetime (movement_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Seguimiento de movimientos de envases (salidas y devoluciones)';

-- ============================================================
-- 4. TABLAS DE COMPRAS Y LOTES CON VENCIMIENTO
-- ============================================================

CREATE TABLE IF NOT EXISTS purchases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL,
    document_type_id BIGINT UNSIGNED NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    purchase_datetime TIMESTAMP NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
    purchase_invoice VARCHAR(255) COMMENT 'URL o ruta de la factura del proveedor',
    pdf_url VARCHAR(255) COMMENT 'URL del PDF generado de la compra',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE RESTRICT,
    KEY idx_status (status),
    KEY idx_purchase_datetime (purchase_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de compras a proveedores';

CREATE TABLE IF NOT EXISTS purchase_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INTEGER NOT NULL,
    unit_cost DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalles de línea de cada compra';

CREATE TABLE IF NOT EXISTS product_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_number VARCHAR(100) NOT NULL UNIQUE COMMENT 'Identificador único del lote (LOT-2025-001)',
    supplier_id BIGINT UNSIGNED,
    purchase_detail_id BIGINT UNSIGNED,
    quantity_received INTEGER NOT NULL COMMENT 'Cantidad recibida del lote',
    quantity_available INTEGER NOT NULL COMMENT 'Cantidad disponible en este momento',
    quantity_expired INTEGER DEFAULT 0 COMMENT 'Cantidad que ha vencido',
    manufacturing_date DATE COMMENT 'Fecha de manufactura/producción',
    expiration_date DATE NOT NULL COMMENT 'Fecha de vencimiento del lote',
    storage_location VARCHAR(255) COMMENT 'Ubicación en almacén',
    status ENUM('received', 'in_stock', 'partial', 'expired', 'disposed') DEFAULT 'in_stock' COMMENT 'Estado del lote',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (purchase_detail_id) REFERENCES purchase_details(id) ON DELETE SET NULL,
    KEY idx_batch_number (batch_number),
    KEY idx_expiration_date (expiration_date),
    KEY idx_status (status),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lotes de productos con control de vencimiento (CRÍTICO para cervezas, mecatos, comestibles)';

CREATE TABLE IF NOT EXISTS batch_expiration_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id BIGINT UNSIGNED NOT NULL,
    alert_type ENUM('7_days', '3_days', 'expired') NOT NULL COMMENT '7_days=alerta a 7 días, 3_days=alerta a 3 días, expired=ya vencido',
    alert_datetime TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'acknowledged', 'expired') DEFAULT 'pending',
    notified_user_id BIGINT UNSIGNED COMMENT 'Usuario a quien se notificó',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_id) REFERENCES product_batches(id) ON DELETE CASCADE,
    FOREIGN KEY (notified_user_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_alert_type (alert_type),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistema de alertas automáticas para lotes próximos a vencer';

-- ============================================================
-- 5. TABLAS DE VENTAS Y PAGOS MIXTOS
-- ============================================================

CREATE TABLE IF NOT EXISTS sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Vendedor que realizó la venta',
    document_type_id BIGINT UNSIGNED NOT NULL,
    cash_shift_id BIGINT UNSIGNED COMMENT 'Turno de caja en el que se realizó la venta',
    sale_number VARCHAR(100) UNIQUE NOT NULL COMMENT 'Número de venta único',
    subtotal DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0 COMMENT 'Impuestos sobre la venta',
    discount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Descuentos aplicados',
    container_deposits DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total de depósitos de envases',
    total_price DECIMAL(10, 2) NOT NULL COMMENT 'Precio total final',
    paid_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cantidad pagada por cliente',
    change_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Cambio devuelto',
    debt_amount DECIMAL(10, 2) DEFAULT 0 COMMENT 'Deuda si es venta a crédito',
    is_credit BOOLEAN DEFAULT FALSE COMMENT 'Indica si es venta a crédito',
    refund_requested BOOLEAN DEFAULT FALSE COMMENT 'Cliente solicitó reembolso',
    refund_amount DECIMAL(10, 2) DEFAULT 0,
    sale_datetime TIMESTAMP NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
    invoice_number VARCHAR(255),
    pdf_url VARCHAR(255) COMMENT 'URL del PDF de la venta',
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (cash_shift_id) REFERENCES cash_shifts(id) ON DELETE SET NULL,
    KEY idx_sale_number (sale_number),
    KEY idx_status (status),
    KEY idx_sale_datetime (sale_datetime),
    KEY idx_is_credit (is_credit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de todas las ventas realizadas';

CREATE TABLE IF NOT EXISTS sale_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED COMMENT 'Lote específico del que se vendió',
    quantity INTEGER NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    container_quantity INTEGER DEFAULT 0 COMMENT 'Cantidad de envases vendidos',
    container_deposit DECIMAL(10, 2) DEFAULT 0 COMMENT 'Depósito cobrado por envase',
    expiration_date DATE COMMENT 'Fecha de vencimiento del producto vendido',
    received_quantity INTEGER COMMENT 'Cantidad recibida (puede diferir de ordenado)',
    rejection_reason TEXT COMMENT 'Razón por la cual el cliente rechazó el producto',
    is_returned BOOLEAN DEFAULT FALSE COMMENT 'Indica si fue devuelto',
    returned_date DATE,
    return_reason TEXT COMMENT 'Razón de la devolución',
    original_quantity INTEGER COMMENT 'Cantidad original que se vendió',
    returned_quantity INTEGER DEFAULT 0 COMMENT 'Cantidad que fue devuelta',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES product_batches(id) ON DELETE SET NULL,
    KEY idx_sale_id (sale_id),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalles de línea de cada venta con control de devoluciones y lotes';

CREATE TABLE IF NOT EXISTS sale_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('cash', 'transfer', 'card', 'check', 'credit') NOT NULL COMMENT 'Método de pago (CRÍTICO: permite pagos mixtos)',
    amount DECIMAL(10, 2) NOT NULL COMMENT 'Cantidad pagada con este método',
    reference_number VARCHAR(255) COMMENT 'Número de transferencia, referencia de tarjeta, etc',
    payment_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    KEY idx_payment_method (payment_method),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Desglose de pagos mixtos (50% transfer + 50% cash, etc) - TABLA CRÍTICA';

-- ============================================================
-- 6. TABLAS DE DEVOLUCIONES Y REEMBOLSOS
-- ============================================================

CREATE TABLE IF NOT EXISTS sale_refunds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    refund_type ENUM('full', 'partial') NOT NULL COMMENT 'Reembolso completo o parcial',
    original_amount DECIMAL(10, 2) NOT NULL COMMENT 'Monto original de la venta',
    refund_amount DECIMAL(10, 2) NOT NULL COMMENT 'Monto a reembolsar',
    reason VARCHAR(255) NOT NULL COMMENT 'Razón: no_lo_que_pidio, defectuoso, error, cambio_idea',
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_by BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que solicitó el reembolso',
    approved_by BIGINT UNSIGNED COMMENT 'Usuario que aprobó el reembolso (gerente)',
    refund_datetime TIMESTAMP COMMENT 'Fecha/hora en que se procesó el reembolso',
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_status (status),
    KEY idx_refund_datetime (refund_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistema de devoluciones/reembolsos (cliente rechaza producto) - TABLA CRÍTICA';

CREATE TABLE IF NOT EXISTS refund_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    refund_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('cash', 'transfer', 'card') NOT NULL COMMENT 'Método de reembolso (mismo que se pagó)',
    amount DECIMAL(10, 2) NOT NULL,
    reference_number VARCHAR(255) COMMENT 'Número de transacción de devolución',
    payment_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (refund_id) REFERENCES sale_refunds(id) ON DELETE CASCADE,
    KEY idx_payment_method (payment_method),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Desglose de reembolsos por método (debe devolver por el mismo método)';

-- ============================================================
-- 7. TABLAS DE CRÉDITOS Y CUENTAS POR COBRAR
-- ============================================================

CREATE TABLE IF NOT EXISTS customer_credits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    credit_limit DECIMAL(10, 2) DEFAULT 0 COMMENT 'Límite de crédito permitido',
    current_debt DECIMAL(10, 2) DEFAULT 0 COMMENT 'Deuda actual del cliente',
    credit_enabled BOOLEAN DEFAULT FALSE COMMENT 'Si el cliente tiene crédito habilitado',
    payment_days INTEGER DEFAULT 30 COMMENT 'Días permitidos para pagar',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_customer (customer_id),
    KEY idx_current_debt (current_debt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de créditos otorgados a clientes';

CREATE TABLE IF NOT EXISTS credit_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    sale_id BIGINT UNSIGNED COMMENT 'Venta a crédito que se está pagando',
    amount DECIMAL(10, 2) NOT NULL COMMENT 'Monto del pago a crédito',
    payment_method ENUM('cash', 'transfer', 'card') DEFAULT 'cash',
    payment_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Vendedor que recibió el pago',
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_payment_datetime (payment_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pagos de deudas a crédito';

-- ============================================================
-- 8. TABLAS DE GESTIÓN DE CAJA Y TURNOS
-- ============================================================

CREATE TABLE IF NOT EXISTS cash_registers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Nombre de la caja (Caja 1, Caja Principal, etc)',
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cajas registradoras del negocio';

CREATE TABLE IF NOT EXISTS cash_shifts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cash_register_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Vendedor/cajero del turno',
    opening_balance DECIMAL(10, 2) NOT NULL COMMENT 'Dinero inicial de la caja',
    expected_balance DECIMAL(10, 2) COMMENT 'Saldo esperado al cierre',
    actual_balance DECIMAL(10, 2) COMMENT 'Saldo real al cierre',
    difference DECIMAL(10, 2) COMMENT 'Diferencia (sobrante o faltante)',
    opening_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closing_datetime TIMESTAMP,
    status ENUM('open', 'closed') DEFAULT 'open',
    opening_notes TEXT,
    closing_notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_status (status),
    KEY idx_opening_datetime (opening_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Turnos de caja (apertura y cierre diarios)';

CREATE TABLE IF NOT EXISTS cash_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cash_shift_id BIGINT UNSIGNED NOT NULL,
    type ENUM('sale', 'expense', 'withdrawal', 'deposit') NOT NULL COMMENT 'Tipo de movimiento',
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'credit') DEFAULT 'cash',
    reference_id BIGINT UNSIGNED COMMENT 'ID de la venta, gasto, etc',
    description TEXT NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cash_shift_id) REFERENCES cash_shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimientos dentro de un turno de caja';

-- ============================================================
-- 9. TABLAS DE DESCUENTOS Y PROMOCIONES
-- ============================================================

CREATE TABLE IF NOT EXISTS discounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('percentage', 'fixed_amount') NOT NULL COMMENT 'Porcentaje o monto fijo',
    value DECIMAL(10, 2) NOT NULL COMMENT 'Valor del descuento',
    start_date DATE COMMENT 'Inicio de la promoción',
    end_date DATE COMMENT 'Fin de la promoción',
    min_purchase_amount DECIMAL(10, 2) COMMENT 'Compra mínima para aplicar',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Descuentos y promociones disponibles';

CREATE TABLE IF NOT EXISTS discount_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    discount_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (discount_id) REFERENCES discounts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_discount_product (discount_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Productos que aplican a cada descuento';

-- ============================================================
-- 10. TABLAS DE GASTOS
-- ============================================================

CREATE TABLE IF NOT EXISTS expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    concept VARCHAR(255) NOT NULL COMMENT 'Concepto del gasto',
    description TEXT,
    amount DECIMAL(10, 2) NOT NULL,
    category ENUM('operational', 'salary', 'taxes', 'maintenance', 'other') DEFAULT 'other',
    cash_shift_id BIGINT UNSIGNED COMMENT 'Turno de caja asociado',
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario que registró el gasto',
    expense_datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    receipt_url VARCHAR(255) COMMENT 'Foto/PDF del comprobante de gasto',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cash_shift_id) REFERENCES cash_shifts(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_category (category),
    KEY idx_expense_datetime (expense_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gastos registrados en la licorera';

-- ============================================================
-- 11. TABLAS DE INVENTARIO Y TRANSACCIONES
-- ============================================================

CREATE TABLE IF NOT EXISTS inventories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    type ENUM('purchase', 'sale', 'adjustment') NOT NULL COMMENT 'Tipo de movimiento de inventario',
    quantity INTEGER NOT NULL COMMENT 'Cantidad (positiva o negativa)',
    reason TEXT COMMENT 'Razón del movimiento',
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_type (type),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de movimientos de inventario';

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('purchase', 'sale', 'payment', 'refund', 'adjustment') NOT NULL COMMENT 'Tipo de transacción',
    amount DECIMAL(10, 2) NOT NULL,
    reference_id BIGINT UNSIGNED COMMENT 'ID de la venta, compra, etc',
    description TEXT,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_type (type),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de todas las transacciones financieras del sistema';

-- ============================================================
-- 12. TABLAS DE REPORTES Y RESÚMENES
-- ============================================================

CREATE TABLE IF NOT EXISTS daily_summaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    summary_date DATE NOT NULL UNIQUE COMMENT 'Fecha del resumen diario',
    total_sales DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total de ventas del día',
    total_purchases DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total de compras del día',
    total_expenses DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total de gastos del día',
    cash_sales DECIMAL(10, 2) DEFAULT 0 COMMENT 'Ventas en efectivo',
    card_sales DECIMAL(10, 2) DEFAULT 0 COMMENT 'Ventas con tarjeta',
    transfer_sales DECIMAL(10, 2) DEFAULT 0 COMMENT 'Ventas por transferencia',
    credit_sales DECIMAL(10, 2) DEFAULT 0 COMMENT 'Ventas a crédito',
    sales_count INTEGER DEFAULT 0 COMMENT 'Número de transacciones',
    containers_out DECIMAL(10, 2) DEFAULT 0 COMMENT 'Depósitos de envases cobrados',
    containers_returned DECIMAL(10, 2) DEFAULT 0 COMMENT 'Depósitos de envases devueltos',
    total_refunds DECIMAL(10, 2) DEFAULT 0 COMMENT 'Total de reembolsos',
    products_expired INTEGER DEFAULT 0 COMMENT 'Productos vencidos',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_summary_date (summary_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Resumen diario automático para reportes y análisis';

-- ============================================================
-- 13. TABLAS DE CONTROL DEL SISTEMA
-- ============================================================

CREATE TABLE IF NOT EXISTS cache (
    key_name VARCHAR(255) NOT NULL PRIMARY KEY,
    value LONGTEXT NOT NULL,
    expiration INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Caché del sistema';

CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    reserved_at INT UNSIGNED,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    KEY idx_queue (queue),
    KEY idx_reserved_at (reserved_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cola de trabajos/tareas';

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY idx_user_id (user_id),
    KEY idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones activas del sistema';

-- ============================================================
-- ÍNDICES PARA OPTIMIZACIÓN DE CONSULTAS
-- ============================================================

CREATE INDEX idx_sales_customer_datetime ON sales(customer_id, sale_datetime);
CREATE INDEX idx_sales_user_datetime ON sales(user_id, sale_datetime);
CREATE INDEX idx_purchase_details_purchase ON purchase_details(purchase_id);
CREATE INDEX idx_sale_details_sale ON sale_details(sale_id);
CREATE INDEX idx_product_batches_product ON product_batches(product_id);
CREATE INDEX idx_container_movements_datetime ON container_movements(movement_datetime);
CREATE INDEX idx_sale_payments_datetime ON sale_payments(payment_datetime);
CREATE INDEX idx_refund_payments_refund ON refund_payments(refund_id);
CREATE INDEX idx_cash_movements_shift ON cash_movements(cash_shift_id);
CREATE INDEX idx_expenses_datetime ON expenses(expense_datetime);
CREATE INDEX idx_transactions_user_datetime ON transactions(user_id, created_at);

-- ============================================================
-- RELACIONES FINALES Y RESTRICCIONES
-- ============================================================

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- FIN DEL MODELO ENTIDAD-RELACIÓN
-- Sistema POS Licorera "Moes Lucianna"
-- Versión: 1.0 - Completo
-- Fecha de Creación: 2025-10-18
-- ============================================================
