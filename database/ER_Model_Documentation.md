# 📊 Modelo Entidad-Relación (EER) - Sistema POS Licorera "Moes Lucianna"

## Versión: 1.0 | Fecha: 2025-10-18 | Estado: Completo

---

## 📋 Contenido

1. [Visión General](#visión-general)
2. [Grupo de Tablas por Dominio](#grupo-de-tablas-por-dominio)
3. [Diagrama de Relaciones](#diagrama-de-relaciones)
4. [Descripción Detallada de Tablas](#descripción-detallada-de-tablas)
5. [Características Críticas](#características-críticas)

---

## 🎯 Visión General

El modelo soporta un **Sistema POS robusto para una licorera** que vende:
- 🍺 **Cervezas** (con envases retornables)
- 🍕 **Mecatos/Comestibles** (con fecha de vencimiento)
- 🎯 **Servicios especiales** (combo, etc)

**Requisitos implementados:**
- ✅ Pagos mixtos (Cash + Transfer)
- ✅ Control de vencimiento por lote
- ✅ Sistema de devoluciones/reembolsos
- ✅ Gestión de envases retornables con depósitos
- ✅ Créditos a clientes
- ✅ Turnos de caja con arqueo
- ✅ Descuentos y promociones
- ✅ Gastos y caja
- ✅ Reportes diarios

---

## 📦 Grupo de Tablas por Dominio

### 1️⃣ **SEGURIDAD Y USUARIOS**
```
roles
└── users
```

### 2️⃣ **CATÁLOGOS Y MAESTROS**
```
categories
suppliers
customers
document_types
```

### 3️⃣ **PRODUCTOS Y ENVASES** (NÚCLEO)
```
products
├── product_images
├── product_containers (con container_movements)
│   └── containers
└── product_batches (CRÍTICO: lotes con vencimiento)
    └── batch_expiration_alerts
```

### 4️⃣ **COMPRAS**
```
purchases
├── purchase_details
└── product_batches (relación por purchase_detail_id)
```

### 5️⃣ **VENTAS** (NÚCLEO)
```
sales (pagos, crédito, etc)
├── sale_details (con batch_id, devoluciones)
├── sale_payments (CRÍTICO: desglose de pagos mixtos)
└── sale_refunds (CRÍTICO: devoluciones/reembolsos)
    └── refund_payments
```

### 6️⃣ **CRÉDITOS Y CxC**
```
customer_credits
└── credit_payments
```

### 7️⃣ **CAJA Y TURNOS** (CRÍTICO)
```
cash_registers
└── cash_shifts
    └── cash_movements
```

### 8️⃣ **DESCUENTOS**
```
discounts
└── discount_products
```

### 9️⃣ **GASTOS**
```
expenses
```

### 🔟 **INVENTARIO Y TRANSACCIONES**
```
inventories
transactions
```

### 1️⃣1️⃣ **REPORTES**
```
daily_summaries
```

---

## 🔗 Diagrama de Relaciones

```
                         ┌─── roles ───┐
                         │             │
                         └─── users ───┤
                                       │
                    ┌──────────────────┼──────────────────┐
                    │                  │                  │
              categories         suppliers            customers
                    │                  │                  │
                    │                  │                  ├─── customer_credits
                    │                  │                  │      └─── credit_payments
                    │                  │                  │
                    └───────┬──────────┴──────────────────┘
                            │
                       products ◄─── user_id
                    ┌─────────┴────────┐
                    │                  │
            product_images      product_containers
                              ◄────────┤
                                       │
                                  containers
                                       │
                              container_movements
                                       │
                                   customers (returns)


            document_types
                    ├──── purchases
                    │      ├─── purchase_details
                    │      │      └─── products
                    │      │             ├─── product_batches ◄─ LOTES CON VENCIMIENTO
                    │      │             │      └─── batch_expiration_alerts
                    │      │             │
                    │      └─── product_batches
                    │
                    └──── sales ◄─── cash_shifts
                           ├─── sale_details
                           │    ├─── products
                           │    ├─── product_batches (batch_id)
                           │    └─── sale_refunds ◄─ DEVOLUCIONES
                           │           └─── refund_payments
                           │
                           └─── sale_payments ◄─ PAGOS MIXTOS (50% cash + 50% transfer)
                                ├─── cash (payment_method=cash)
                                ├─── transfer (payment_method=transfer)
                                ├─── card (payment_method=card)
                                └─── credit (payment_method=credit)

            cash_registers
                    │
            cash_shifts
                    ├─── cash_movements (type: sale, expense, withdrawal, deposit)
                    │
                    └─── sales (cash_shift_id)

            discounts
                    │
            discount_products
                    │
                products

            expenses
                    │
            cash_shifts

            inventories ◄─ Historial de movimientos
            transactions ◄─ Historial de transacciones

            daily_summaries ◄─ Resumen automático diario
```

---

## 📄 Descripción Detallada de Tablas

### **TABLAS BASE**

#### 🔐 `roles`
- Roles del sistema (Admin, Vendedor, Gerente, Supervisor)
- Campo: `name` (unique)

#### 👤 `users`
- Usuarios del sistema
- FK: `role_id` → roles
- Campos: name, email, phone, address, photo, password

---

### **CATÁLOGOS**

#### 📁 `categories`
- Categorías de productos
- Ejemplos: Cervezas, Mecatos, Comestibles, Bebidas No Alcohólicas

#### 🏢 `suppliers`
- Proveedores/Distribuidores
- Campos: rut, name, email, phone, address, status

#### 👥 `customers`
- Clientes que compran
- Campos: nit, name, email, phone, address, status

#### 📝 `document_types`
- Tipos de documentos (Factura, Boleta, Nota Crédito, Remito, etc)
- Campo: `type` (purchase | sale)

---

### **PRODUCTOS Y ENVASES** ⭐ CRÍTICO

#### 🛍️ `products`
- Catálogo de productos
- FK: `category_id` → categories, `user_id` → users
- Campos importantes:
  - `has_expiration` (BOOLEAN) - ¿Tiene vencimiento?
  - `expiration_alert_days` (INT) - Días para alertar
  - `has_container` (BOOLEAN) - ¿Es retornable?
  - `alcohol_percentage`, `volume_ml`
  - `requires_id` - Requiere verificación de cédula
  - `is_combo` - Es un combo

#### 🖼️ `product_images`
- Imágenes de productos

#### 📦 `containers`
- Catálogo de envases (botellas, cajas, barriles)
- Campos: name, deposit_price, type (bottle|case|keg)

#### 🔗 `product_containers`
- Relación N:N entre productos y contenedores
- Ejemplo: 1 caja de cerveza = 24 botellas
- Campos: quantity (cantidad de envases por producto)

#### 🚚 `container_movements`
- Seguimiento de envases (salidas y devoluciones)
- FK: container_id, customer_id, sale_id, user_id
- Campo: `type` (out | return)
- Campos: quantity, deposit_amount

---

### **LOTES Y VENCIMIENTO** ⭐ CRÍTICO

#### 📊 `product_batches`
- Lotes de productos con control de vencimiento
- **TABLA CRÍTICA para control de cervezas, mecatos y comestibles**
- FK: product_id, supplier_id, purchase_detail_id
- Campos:
  - `batch_number` (UNIQUE) - Identificador del lote
  - `quantity_received` - Cantidad recibida
  - `quantity_available` - Disponible en este momento
  - `quantity_expired` - Cantidad vencida
  - `manufacturing_date` - Fecha de producción
  - `expiration_date` - **FECHA DE VENCIMIENTO**
  - `status` (received|in_stock|partial|expired|disposed)

#### ⚠️ `batch_expiration_alerts`
- Alertas automáticas para lotes próximos a vencer
- Campos: alert_type (7_days|3_days|expired)
- Campos: status (pending|acknowledged|expired)

---

### **COMPRAS**

#### 🛒 `purchases`
- Registro de compras a proveedores
- FK: supplier_id, user_id, document_type_id
- Campos: total_cost, purchase_datetime, status, purchase_invoice, pdf_url

#### 📋 `purchase_details`
- Detalles de compra
- FK: purchase_id, product_id
- Campos: quantity, unit_cost, subtotal

---

### **VENTAS** ⭐ CRÍTICO

#### 💳 `sales`
- Registro de todas las ventas
- FK: customer_id, user_id, document_type_id, cash_shift_id
- Campos importantes:
  - `sale_number` (UNIQUE) - Número de venta
  - `subtotal, tax, discount` - Detalles de precio
  - `container_deposits` - Depósitos de envases
  - `total_price` - **Precio final**
  - `paid_amount, change_amount` - Efectivo
  - `debt_amount, is_credit` - Crédito
  - `refund_requested, refund_amount` - Reembolso
  - `status` (pending|completed|cancelled)

#### 📦 `sale_details`
- Detalles de venta
- FK: sale_id, product_id, batch_id (CRÍTICO: permite trazabilidad de lotes)
- Campos especiales:
  - `batch_id` - Lote específico vendido
  - `container_quantity, container_deposit` - Envase asociado
  - `is_returned` - ¿Fue devuelto?
  - `return_reason` - Razón de devolución

#### 💰 `sale_payments` ⭐ CRÍTICO - PAGOS MIXTOS
- **Desglose de pagos por método**
- **Permite registrar: 50% transfer + 50% cash, etc**
- FK: sale_id
- Campos:
  - `payment_method` (cash|transfer|card|check|credit)
  - `amount` - Monto de este método
  - `reference_number` - Número de transferencia, etc
  - `status` (pending|completed|failed)
- ⚠️ Una venta puede tener múltiples registros aquí (uno por método)

#### 🔄 `sale_refunds` ⭐ CRÍTICO - DEVOLUCIONES
- Sistema de devoluciones/reembolsos
- FK: sale_id
- Campos:
  - `refund_type` (full|partial)
  - `reason` - Por qué se rechazó (no_lo_que_pidio|defectuoso|error|cambio_idea)
  - `status` (pending|approved|rejected|completed)
  - `approved_by` - Gerente que aprobó

#### ↩️ `refund_payments`
- Desglose de reembolsos por método
- Debe devolverse por el mismo método de pago

---

### **CRÉDITOS Y CxC**

#### 💵 `customer_credits`
- Control de créditos otorgados
- FK: customer_id
- Campos: credit_limit, current_debt, credit_enabled, payment_days

#### 💸 `credit_payments`
- Pagos de deudas a crédito
- FK: customer_id, sale_id, user_id
- Campos: amount, payment_datetime

---

### **CAJA Y TURNOS** ⭐ CRÍTICO

#### 💼 `cash_registers`
- Cajas registradoras (Caja 1, Caja Principal, etc)

#### 🕐 `cash_shifts`
- Turnos de caja (apertura y cierre diarios)
- FK: cash_register_id, user_id
- Campos:
  - `opening_balance` - Dinero inicial
  - `expected_balance` - Saldo esperado
  - `actual_balance` - Saldo real al cierre
  - `difference` - Sobrante/faltante
  - `status` (open|closed)

#### 🔄 `cash_movements`
- Movimientos dentro de un turno
- Campo: `type` (sale|expense|withdrawal|deposit)
- FK: cash_shift_id, user_id

---

### **DESCUENTOS**

#### 🎁 `discounts`
- Descuentos y promociones
- Campo: `type` (percentage|fixed_amount)

#### 🏷️ `discount_products`
- Productos que aplican a cada descuento

---

### **GASTOS**

#### 💸 `expenses`
- Gastos registrados
- Categorías: operational, salary, taxes, maintenance, other
- FK: cash_shift_id, user_id

---

### **INVENTARIO Y TRANSACCIONES**

#### 📊 `inventories`
- Historial de movimientos de inventario
- Campo: `type` (purchase|sale|adjustment)

#### 💹 `transactions`
- Historial de todas las transacciones financieras
- Campo: `type` (purchase|sale|payment|refund|adjustment)

---

### **REPORTES**

#### 📈 `daily_summaries`
- Resumen diario automático
- Campos:
  - `total_sales, total_purchases, total_expenses`
  - `cash_sales, card_sales, transfer_sales, credit_sales`
  - `containers_out, containers_returned`
  - `total_refunds`
  - `products_expired`

---

## ⚡ Características Críticas

### 1. **Pagos Mixtos** 💰
```
Una venta de $100 se puede pagar:
- 50% transfer ($50) → sale_payments con method=transfer
- 50% cash ($50) → sale_payments con method=cash

Resultado: 2 registros en sale_payments para la misma venta
```

### 2. **Control de Vencimiento** 📅
```
Compra → purchase_details → product_batches (con expiration_date)
                                    ↓
                            batch_expiration_alerts (alertas automáticas)
                                    ↓
Venta → sale_details → batch_id (trazabilidad completa)
```

### 3. **Devoluciones/Reembolsos** 🔄
```
Venta completada
        ↓
Cliente solicita devolución
        ↓
Crear sale_refunds (pending)
        ↓
Gerente aprueba
        ↓
Crear refund_payments (mismo método de pago)
        ↓
Revertir inventario (sale_details.is_returned=true)
```

### 4. **Envases Retornables** 📦
```
Venta de cerveza + envase
        ↓
container_movements (type=out, quantity, deposit_amount)
        ↓
Cliente devuelve envase
        ↓
container_movements (type=return, deposit_amount como crédito)
```

### 5. **Turnos de Caja** 🕐
```
Apertura → cash_shifts (opening_balance)
            ↓
Ventas → cash_movements (type=sale)
Gastos → cash_movements (type=expense)
Retiros → cash_movements (type=withdrawal)
Depósitos → cash_movements (type=deposit)
            ↓
Cierre → cash_shifts (actual_balance, difference)
```

### 6. **Créditos a Clientes** 💳
```
customer_credits (credit_limit, current_debt)
        ↓
sales (is_credit=true, debt_amount)
        ↓
credit_payments (abonos parciales del cliente)
```

---

## 🔍 Índices Principales

```sql
-- Consultas de ventas frecuentes
idx_sales_customer_datetime
idx_sales_user_datetime
idx_sale_details_sale

-- Alertas de vencimiento
idx_product_batches_product
idx_batch_expiration_date (critical)

-- Movimientos de caja
idx_cash_movements_shift
idx_expenses_datetime

-- Reportes
idx_transactions_user_datetime
idx_sales_datetime
```

---

## 🚀 Casos de Uso Principales

### ✅ Venta Normal en Caja
1. Crear `sales`
2. Agregar `sale_details` (con batch_id si aplica)
3. Registrar `sale_payments` (puede tener múltiples registros)
4. Actualizar `inventories` (sale)
5. Registrar `container_movements` si aplica

### ✅ Devolución de Producto
1. Crear `sale_refunds`
2. Crear `refund_payments`
3. Actualizar `sale_details` (is_returned=true)
4. Revertir `inventories`
5. Revertir `container_movements`

### ✅ Cierre de Turno
1. Cerrar `cash_shifts`
2. Calcular `cash_movements` totales
3. Generar `daily_summaries`

### ✅ Alerta de Vencimiento
1. Query: `product_batches` con `expiration_date` próximo
2. Crear `batch_expiration_alerts`
3. Notificar usuario

---

## 📊 SQL Para Consultas Comunes

### Venta con desglose de pagos
```sql
SELECT s.id, s.sale_number, s.total_price,
       sp.payment_method, sp.amount
FROM sales s
LEFT JOIN sale_payments sp ON s.id = sp.sale_id
WHERE s.sale_datetime >= CURDATE()
ORDER BY s.sale_datetime DESC;
```

### Lotes próximos a vencer
```sql
SELECT pb.id, pb.batch_number, p.name, pb.expiration_date,
       pb.quantity_available
FROM product_batches pb
JOIN products p ON pb.product_id = p.id
WHERE pb.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  AND pb.status IN ('in_stock', 'partial')
ORDER BY pb.expiration_date ASC;
```

### Envases por devolver
```sql
SELECT c.name, cm.quantity, cm.deposit_amount, cus.name as customer
FROM container_movements cm
JOIN containers c ON cm.container_id = c.id
JOIN customers cus ON cm.customer_id = cus.id
WHERE cm.type = 'out'
  AND cm.movement_datetime >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY cm.movement_datetime DESC;
```

### Resumen de pagos mixtos del día
```sql
SELECT sp.payment_method, SUM(sp.amount) as total
FROM sale_payments sp
JOIN sales s ON sp.sale_id = s.id
WHERE DATE(s.sale_datetime) = CURDATE()
GROUP BY sp.payment_method;
```

---

## 📌 Notas Importantes

1. **Backup Regular**: El control de vencimiento es crítico
2. **Alertas Automáticas**: Implementar job para `batch_expiration_alerts`
3. **Auditoría**: Todos los cambios registrados en `transactions`
4. **Reportes Diarios**: Generar `daily_summaries` automáticamente
5. **Permisos**: Validar rol de usuario para aprobar reembolsos

---

**Generado:** 2025-10-18  
**Versión:** 1.0 - Completo  
**Estado:** Listo para implementación en MySQL Workbench
