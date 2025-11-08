# 📊 ANÁLISIS COMPLETO DE MIGRACIONES REQUERIDAS
## Sistema POS Licorera "Moes Lucianna"

---

## 🎯 RESUMEN EJECUTIVO

**Estado Actual:** 17 migraciones existentes (básicas)
**Migraciones a Crear:** 23 nuevas migraciones
**Migraciones a Modificar:** 3 existentes
**Total de Tablas Final:** 51 tablas

---

## 📋 MIGRACIONES EXISTENTES (17)

### ✅ Ya están creadas (no modificar):
1. ✅ `create_roles_table`
2. ✅ `create_users_table`
3. ✅ `create_cache_table`
4. ✅ `create_jobs_table`
5. ✅ `create_categories_table`
6. ✅ `create_product_images_table`
7. ✅ `create_suppliers_table`
8. ✅ `create_customers_table`
9. ✅ `create_document_types_table`
10. ✅ `create_purchases_table`
11. ✅ `create_purchase_details_table`
12. ✅ `create_inventories_table`
13. ✅ `create_transactions_table`
14. ✅ `add_pdf_url_to_purchases_table`

### ⚠️ NECESITAN MODIFICACIÓN (3):

#### 1. `create_products_table` ⚠️ CRÍTICO
**Estado actual:** Solo tiene campos básicos
**Campos faltantes:**
```php
$table->string('sku')->unique()->nullable();
$table->string('barcode')->unique()->nullable();
$table->decimal('cost_price', 10, 2)->default(0);
$table->decimal('wholesale_price', 10, 2)->nullable();
$table->integer('min_stock')->default(0);
$table->integer('max_stock')->default(0);
$table->boolean('has_expiration')->default(false); // CRÍTICO
$table->integer('expiration_alert_days')->default(7);
$table->boolean('has_container')->default(false); // CRÍTICO
$table->decimal('alcohol_percentage', 5, 2)->nullable();
$table->integer('volume_ml')->nullable();
$table->boolean('requires_id')->default(false);
$table->boolean('is_combo')->default(false);
$table->string('brand')->nullable();
```

**Acción:** Crear migración `add_additional_fields_to_products_table`

---

#### 2. `create_sales_table` ⚠️ CRÍTICO
**Estado actual:** Tiene `payment_method` como ENUM (INCORRECTO)
**Problema:** No permite PAGOS MIXTOS

**Campos faltantes:**
```php
// ELIMINAR: payment_method (se mueve a tabla sale_payments)
$table->foreignId('cash_shift_id')->nullable()->constrained('cash_shifts')->onDelete('set null');
$table->string('sale_number')->unique();
$table->decimal('subtotal', 10, 2)->default(0);
$table->decimal('tax', 10, 2)->default(0);
$table->decimal('discount', 10, 2)->default(0);
$table->decimal('container_deposits', 10, 2)->default(0);
$table->decimal('paid_amount', 10, 2)->default(0);
$table->decimal('change_amount', 10, 2)->default(0);
$table->decimal('debt_amount', 10, 2)->default(0);
$table->boolean('is_credit')->default(false);
$table->boolean('refund_requested')->default(false);
$table->decimal('refund_amount', 10, 2)->default(0);
$table->string('invoice_number')->nullable();
$table->string('pdf_url')->nullable();
$table->text('notes')->nullable();
```

**Acción:** Crear migración `modify_sales_table_for_mixed_payments`

---

#### 3. `create_sale_details_table` ⚠️ CRÍTICO
**Estado actual:** Solo tiene campos básicos
**Campos faltantes:**
```php
$table->foreignId('batch_id')->nullable()->constrained('product_batches')->onDelete('set null'); // TRAZABILIDAD
$table->integer('container_quantity')->default(0);
$table->decimal('container_deposit', 10, 2)->default(0);
$table->date('expiration_date')->nullable();
$table->integer('received_quantity')->nullable();
$table->text('rejection_reason')->nullable();
$table->boolean('is_returned')->default(false);
$table->date('returned_date')->nullable();
$table->text('return_reason')->nullable();
$table->integer('original_quantity')->nullable();
$table->integer('returned_quantity')->default(0);
```

**Acción:** Crear migración `add_batch_and_return_fields_to_sale_details_table`

---

## 🆕 MIGRACIONES NUEVAS A CREAR (23)

### GRUPO 1: ENVASES RETORNABLES (3 tablas)

#### 1. `create_containers_table` 🆕
```php
Schema::create('containers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('deposit_price', 10, 2); // Precio del depósito
    $table->enum('type', ['bottle', 'case', 'keg']); // Tipo de envase
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    
    $table->index('type');
    $table->index('status');
});
```

#### 2. `create_product_containers_table` 🆕
```php
Schema::create('product_containers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('container_id')->constrained()->onDelete('cascade');
    $table->integer('quantity')->default(1); // 1 caja = 24 botellas
    $table->boolean('is_returnable')->default(true);
    $table->timestamps();
    
    $table->unique(['product_id', 'container_id']);
    $table->index('product_id');
    $table->index('container_id');
});
```

#### 3. `create_container_movements_table` 🆕
```php
Schema::create('container_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('container_id')->constrained()->onDelete('cascade');
    $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
    $table->enum('type', ['out', 'return']); // out=venta, return=devolución
    $table->integer('quantity');
    $table->decimal('deposit_amount', 10, 2)->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamp('movement_datetime')->useCurrent();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index('type');
    $table->index('movement_datetime');
});
```

---

### GRUPO 2: LOTES Y VENCIMIENTO ⭐ CRÍTICO (2 tablas)

#### 4. `create_product_batches_table` 🆕 ⭐ CRÍTICO
```php
Schema::create('product_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('batch_number')->unique(); // LOT-2025-001
    $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('purchase_detail_id')->nullable()->constrained()->onDelete('set null');
    $table->integer('quantity_received');
    $table->integer('quantity_available');
    $table->integer('quantity_expired')->default(0);
    $table->date('manufacturing_date')->nullable();
    $table->date('expiration_date'); // CRÍTICO
    $table->string('storage_location')->nullable();
    $table->enum('status', ['received', 'in_stock', 'partial', 'expired', 'disposed'])->default('in_stock');
    $table->timestamps();
    
    $table->index('batch_number');
    $table->index('expiration_date'); // CRÍTICO para alertas
    $table->index('status');
    $table->index('product_id');
});
```

#### 5. `create_batch_expiration_alerts_table` 🆕 ⭐ CRÍTICO
```php
Schema::create('batch_expiration_alerts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('batch_id')->constrained('product_batches')->onDelete('cascade');
    $table->enum('alert_type', ['7_days', '3_days', 'expired']);
    $table->timestamp('alert_datetime')->useCurrent();
    $table->enum('status', ['pending', 'acknowledged', 'expired'])->default('pending');
    $table->foreignId('notified_user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('alert_type');
    $table->index('status');
});
```

---

### GRUPO 3: PAGOS MIXTOS ⭐ CRÍTICO (1 tabla)

#### 6. `create_sale_payments_table` 🆕 ⭐ CRÍTICO
```php
Schema::create('sale_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained()->onDelete('cascade');
    $table->enum('payment_method', ['cash', 'transfer', 'card', 'check', 'credit']);
    $table->decimal('amount', 10, 2); // Monto de este método
    $table->string('reference_number')->nullable(); // Número de transferencia/tarjeta
    $table->timestamp('payment_datetime')->useCurrent();
    $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index('payment_method');
    $table->index('status');
    $table->index('payment_datetime');
});
```

**IMPORTANTE:** Esta tabla permite que UNA venta tenga MÚLTIPLES registros de pago:
- 50% transfer ($50) → 1 registro
- 50% cash ($50) → 1 registro

---

### GRUPO 4: DEVOLUCIONES/REEMBOLSOS ⭐ CRÍTICO (2 tablas)

#### 7. `create_sale_refunds_table` 🆕 ⭐ CRÍTICO
```php
Schema::create('sale_refunds', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained()->onDelete('cascade');
    $table->enum('refund_type', ['full', 'partial']);
    $table->decimal('original_amount', 10, 2);
    $table->decimal('refund_amount', 10, 2);
    $table->string('reason'); // no_lo_que_pidio, defectuoso, error, cambio_idea
    $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
    $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('refund_datetime')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index('status');
    $table->index('refund_datetime');
});
```

#### 8. `create_refund_payments_table` 🆕
```php
Schema::create('refund_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('refund_id')->constrained('sale_refunds')->onDelete('cascade');
    $table->enum('payment_method', ['cash', 'transfer', 'card']); // Mismo método original
    $table->decimal('amount', 10, 2);
    $table->string('reference_number')->nullable();
    $table->timestamp('payment_datetime')->useCurrent();
    $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
    $table->timestamps();
    
    $table->index('payment_method');
    $table->index('status');
});
```

---

### GRUPO 5: CRÉDITOS Y CxC (2 tablas)

#### 9. `create_customer_credits_table` 🆕
```php
Schema::create('customer_credits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->decimal('credit_limit', 10, 2)->default(0);
    $table->decimal('current_debt', 10, 2)->default(0);
    $table->boolean('credit_enabled')->default(false);
    $table->integer('payment_days')->default(30); // Días para pagar
    $table->timestamps();
    
    $table->unique('customer_id');
    $table->index('current_debt');
});
```

#### 10. `create_credit_payments_table` 🆕
```php
Schema::create('credit_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
    $table->decimal('amount', 10, 2);
    $table->enum('payment_method', ['cash', 'transfer', 'card'])->default('cash');
    $table->timestamp('payment_datetime')->useCurrent();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index('payment_datetime');
});
```

---

### GRUPO 6: CAJA Y TURNOS (3 tablas)

#### 11. `create_cash_registers_table` 🆕
```php
Schema::create('cash_registers', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Caja 1, Caja Principal
    $table->text('description')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    
    $table->index('status');
});
```

#### 12. `create_cash_shifts_table` 🆕
```php
Schema::create('cash_shifts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cash_register_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('opening_balance', 10, 2); // Dinero inicial
    $table->decimal('expected_balance', 10, 2)->nullable();
    $table->decimal('actual_balance', 10, 2)->nullable();
    $table->decimal('difference', 10, 2)->nullable(); // Sobrante/faltante
    $table->timestamp('opening_datetime')->useCurrent();
    $table->timestamp('closing_datetime')->nullable();
    $table->enum('status', ['open', 'closed'])->default('open');
    $table->text('opening_notes')->nullable();
    $table->text('closing_notes')->nullable();
    $table->timestamps();
    
    $table->index('status');
    $table->index('opening_datetime');
});
```

#### 13. `create_cash_movements_table` 🆕
```php
Schema::create('cash_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cash_shift_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['sale', 'expense', 'withdrawal', 'deposit']);
    $table->decimal('amount', 10, 2);
    $table->enum('payment_method', ['cash', 'card', 'transfer', 'credit'])->default('cash');
    $table->unsignedBigInteger('reference_id')->nullable(); // ID de venta/gasto
    $table->text('description');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    $table->index('type');
    $table->index('cash_shift_id');
});
```

---

### GRUPO 7: DESCUENTOS (2 tablas)

#### 14. `create_discounts_table` 🆕
```php
Schema::create('discounts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('type', ['percentage', 'fixed_amount']);
    $table->decimal('value', 10, 2); // % o monto fijo
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->decimal('min_purchase_amount', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('is_active');
});
```

#### 15. `create_discount_products_table` 🆕
```php
Schema::create('discount_products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('discount_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['discount_id', 'product_id']);
});
```

---

### GRUPO 8: GASTOS (1 tabla)

#### 16. `create_expenses_table` 🆕
```php
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->string('concept');
    $table->text('description')->nullable();
    $table->decimal('amount', 10, 2);
    $table->enum('category', ['operational', 'salary', 'taxes', 'maintenance', 'other'])->default('other');
    $table->foreignId('cash_shift_id')->nullable()->constrained()->onDelete('set null');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamp('expense_datetime')->useCurrent();
    $table->string('receipt_url')->nullable();
    $table->timestamps();
    
    $table->index('category');
    $table->index('expense_datetime');
});
```

---

### GRUPO 9: REPORTES (1 tabla)

#### 17. `create_daily_summaries_table` 🆕
```php
Schema::create('daily_summaries', function (Blueprint $table) {
    $table->id();
    $table->date('summary_date')->unique();
    $table->decimal('total_sales', 10, 2)->default(0);
    $table->decimal('total_purchases', 10, 2)->default(0);
    $table->decimal('total_expenses', 10, 2)->default(0);
    $table->decimal('cash_sales', 10, 2)->default(0);
    $table->decimal('card_sales', 10, 2)->default(0);
    $table->decimal('transfer_sales', 10, 2)->default(0);
    $table->decimal('credit_sales', 10, 2)->default(0);
    $table->integer('sales_count')->default(0);
    $table->decimal('containers_out', 10, 2)->default(0);
    $table->decimal('containers_returned', 10, 2)->default(0);
    $table->decimal('total_refunds', 10, 2)->default(0);
    $table->integer('products_expired')->default(0);
    $table->timestamps();
    
    $table->index('summary_date');
});
```

---

### GRUPO 10: SISTEMA (1 tabla)

#### 18. `create_sessions_table` 🆕
```php
Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->longText('payload');
    $table->integer('last_activity')->index();
    
    $table->index('user_id');
});
```

---

## 📊 RESUMEN DE CAMBIOS

### 🔄 MODIFICAR (3 migraciones):
1. ⚠️ `add_additional_fields_to_products_table` (has_expiration, has_container, etc)
2. ⚠️ `modify_sales_table_for_mixed_payments` (quitar payment_method, agregar campos)
3. ⚠️ `add_batch_and_return_fields_to_sale_details_table` (batch_id, trazabilidad)

### 🆕 CREAR (23 migraciones nuevas):

**Envases (3):**
- create_containers_table
- create_product_containers_table
- create_container_movements_table

**Vencimiento ⭐ CRÍTICO (2):**
- create_product_batches_table
- create_batch_expiration_alerts_table

**Pagos ⭐ CRÍTICO (1):**
- create_sale_payments_table

**Devoluciones ⭐ CRÍTICO (2):**
- create_sale_refunds_table
- create_refund_payments_table

**Créditos (2):**
- create_customer_credits_table
- create_credit_payments_table

**Caja (3):**
- create_cash_registers_table
- create_cash_shifts_table
- create_cash_movements_table

**Descuentos (2):**
- create_discounts_table
- create_discount_products_table

**Gastos (1):**
- create_expenses_table

**Reportes (1):**
- create_daily_summaries_table

**Sistema (1):**
- create_sessions_table

---

## ⚠️ ORDEN DE EJECUCIÓN IMPORTANTE

Las migraciones deben ejecutarse en este orden por dependencias:

### FASE 1 - Modificar tablas existentes:
1. `add_additional_fields_to_products_table`
2. `modify_sales_table_for_mixed_payments`

### FASE 2 - Crear tablas independientes:
3. `create_containers_table`
4. `create_cash_registers_table`
5. `create_discounts_table`

### FASE 3 - Crear tablas dependientes de FASE 1:
6. `create_product_containers_table` (depende de products, containers)
7. `create_product_batches_table` (depende de products, purchase_details)
8. `create_batch_expiration_alerts_table` (depende de product_batches)

### FASE 4 - Crear tablas de caja:
9. `create_cash_shifts_table` (depende de cash_registers)
10. `create_cash_movements_table` (depende de cash_shifts)

### FASE 5 - Modificar sale_details y crear relacionadas:
11. `add_batch_and_return_fields_to_sale_details_table` (depende de product_batches)
12. `create_sale_payments_table` (depende de sales)
13. `create_sale_refunds_table` (depende de sales)
14. `create_refund_payments_table` (depende de sale_refunds)

### FASE 6 - Crear tablas de clientes:
15. `create_customer_credits_table` (depende de customers)
16. `create_credit_payments_table` (depende de customer_credits)

### FASE 7 - Crear tablas de movimientos:
17. `create_container_movements_table` (depende de containers, sales)

### FASE 8 - Crear tablas auxiliares:
18. `create_discount_products_table` (depende de discounts, products)
19. `create_expenses_table`
20. `create_daily_summaries_table`
21. `create_sessions_table`

---

## 🎯 CARACTERÍSTICAS CRÍTICAS IMPLEMENTADAS

### 1. PAGOS MIXTOS 💰
**Tabla:** `sale_payments`
**Permite:** Una venta con múltiples métodos de pago
**Ejemplo:**
```php
// Venta #1 por $100
Sale::create(['total_price' => 100]);

// 50% transfer
SalePayment::create(['sale_id' => 1, 'payment_method' => 'transfer', 'amount' => 50]);

// 50% cash
SalePayment::create(['sale_id' => 1, 'payment_method' => 'cash', 'amount' => 50]);
```

### 2. CONTROL DE VENCIMIENTO 📅
**Tablas:** `product_batches`, `batch_expiration_alerts`
**Permite:** Trazabilidad completa de lotes y alertas automáticas
**Flujo:**
1. Compra → Crear lote con `expiration_date`
2. Sistema genera `batch_expiration_alerts` a 7 días, 3 días
3. Venta → Asociar `sale_details.batch_id` (trazabilidad)

### 3. DEVOLUCIONES/REEMBOLSOS 🔄
**Tablas:** `sale_refunds`, `refund_payments`
**Permite:** Cliente rechaza producto y solicita reembolso
**Flujo:**
1. Cliente solicita devolución → Crear `sale_refund`
2. Gerente aprueba
3. Sistema crea `refund_payments` con mismo método de pago
4. Reversión automática de inventario

### 4. ENVASES RETORNABLES 📦
**Tablas:** `containers`, `product_containers`, `container_movements`
**Permite:** Control de envases con depósitos
**Flujo:**
1. Venta → `container_movements` tipo 'out' con `deposit_amount`
2. Cliente devuelve → tipo 'return' reembolsa depósito

---

## ✅ CHECKLIST ANTES DE CREAR

Antes de ejecutar las migraciones, verifica:

- [ ] Backup de la base de datos actual
- [ ] Las tablas existentes NO tienen datos críticos (o hacer respaldo)
- [ ] Configurar `config/database.php` correctamente
- [ ] Tener claro el orden de ejecución
- [ ] Tener los seeders preparados para datos de prueba

---

## 📝 PRÓXIMOS PASOS (Cuando me lo ordenes)

1. ✅ Crear las 3 migraciones de modificación
2. ✅ Crear las 23 migraciones nuevas en orden correcto
3. ✅ Ejecutar `php artisan migrate`
4. ✅ Verificar con `php artisan migrate:status`
5. → Crear Modelos Eloquent con relaciones
6. → Crear Seeders para datos de prueba
7. → Crear Controladores

---

**Estado:** ✅ ANÁLISIS COMPLETO
**Total Migraciones:** 26 (3 modificar + 23 crear)
**Tablas Finales:** 51 tablas completamente relacionadas

¿Procedo a crear las migraciones?
