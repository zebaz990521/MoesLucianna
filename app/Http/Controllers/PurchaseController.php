<?php

namespace App\Http\Controllers;


use App\Models\DocumentType;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $purchases = Purchase::with(['supplier', 'user', 'documentType'])->orderBy('purchase_datetime', 'desc')->get();
        return view('purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $suppliers = Supplier::all();
        /* $documentTypes = DocumentType::where('type', 'purchase')->get(); */
        $documentTypes = DocumentType::all();
        $products = Product::all();

        return view('purchase.create', compact('suppliers', 'documentTypes', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        /* dd($request->all());  */



      $request->merge([
        'details' => json_decode($request->input('details'), true)
        ]);

        /* dd($request->details); */

        /* dd($request->input('details')); */
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'document_type_id' => 'required|exists:document_types,id',
            'purchase_datetime' => 'required|date',
            'purchase_invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:pending,completed,cancelled',
            'details' => 'required|array',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Subir la factura si se adjunta
            $invoicePath = null;
            if ($request->hasFile('purchase_invoice')) {
                try {

                    /* OTRA FORMA */
                    /* composer require google/cloud-storage */
                   /*  $file = $request->file('purchase_invoice');

                    // Verificar configuración de GCS
                    $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                    $projectId = env('GOOGLE_CLOUD_PROJECT_ID');
                    $keyFile = env('GOOGLE_CLOUD_KEY_FILE');

                    \Log::info("Intentando subir a GCS - Bucket: {$bucketName}, Project: {$projectId}, KeyFile: {$keyFile}");

                    // Crear estructura de directorios por año y mes
                    $year = date('Y');
                    $month = date('m');
                    $monthName = date('F'); // Nombre del mes en inglés

                    // Directorio base: 2025_facturas/enero/ (ejemplo)
                    $directoryPath = "facturas/compras/{$year}_facturas/{$monthName}";

                    // Generar nombre único para el archivo
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

                    // Subir archivo a Google Cloud Storage con estructura de directorios
                    $fullPath = $directoryPath . '/' . $fileName;

                    \Log::info("Subiendo archivo a GCS: {$fullPath}");

                    // Usar directamente la librería de Google Cloud Storage
                    $keyFilePath = base_path(env('GOOGLE_CLOUD_KEY_FILE'));
                    $credentials = json_decode(file_get_contents($keyFilePath), true);

                    $client = new \Google\Cloud\Storage\StorageClient([
                        'projectId' => $credentials['project_id'],
                        'keyFile' => $credentials
                    ]);

                    $bucket = $client->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                    $object = $bucket->upload($file->getContent(), [
                        'name' => $fullPath,
                        'metadata' => [
                            'cacheControl' => 'public,max-age=86400',
                            'contentType' => $file->getMimeType()
                        ]
                    ]);

                    if ($object) {
                        // Construir URL pública
                        $invoicePath = "https://storage.googleapis.com/" . env('GOOGLE_CLOUD_STORAGE_BUCKET') . "/{$fullPath}";

                        // Log exitoso
                        \Log::info("Factura subida exitosamente a GCS: {$fullPath}");
                        \Log::info("URL pública: {$invoicePath}");
                    } else {
                        throw new \Exception('Error al subir el archivo a Google Cloud Storage');
                    }

                } catch (\Exception $e) {
                    // Log del error
                    \Log::error('Error al subir factura a GCS: ' . $e->getMessage());

                    // Fallback: subir al almacenamiento local con la misma estructura
                    try {
                        $year = date('Y');
                        $monthName = date('F');
                        $directoryPath = "{$year}_facturas/{$monthName}";

                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

                        $invoicePath = $file->storeAs($directoryPath, $fileName, 'public');
                        // Construir URL para almacenamiento local
                        $invoicePath = asset('storage/' . $invoicePath);

                        \Log::info("Factura subida a almacenamiento local como fallback: {$invoicePath}");
                    } catch (\Exception $fallbackError) {
                        \Log::error('Error en fallback local: ' . $fallbackError->getMessage());
                        throw new \Exception('No se pudo subir la factura');
                    }
                } */

                $file = $request->file('purchase_invoice');

                    // Crear estructura de directorios por año y mes
                    date_default_timezone_set('America/Bogota');
                    $year = date('Y');
                    $month = date('m');
                    $monthName = date('F'); // Nombre del mes en inglés

                   /*  dd($monthName);
 */
                    // Directorio base: 2025_facturas/enero/ (ejemplo)
                    $directoryPath = "{$year}_facturas/{$monthName}";

                    // Generar nombre único para el archivo
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    /* dd($originalName); */
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

                    // Subir archivo a Google Cloud Storage con estructura de directorios
                    $fullPath = $directoryPath . '/' . $fileName;
                    $uploadedPath = Storage::disk('gcs')->putFileAs($directoryPath, $file, $fileName, 'public');

                    if ($uploadedPath) {
                        // Construir URL pública manualmente para GCS
                        $bucketName = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                        $invoicePath = "https://storage.googleapis.com/{$bucketName}/{$uploadedPath}";


                        // Log exitoso
                        \Log::info("Factura subida exitosamente a GCS: {$fullPath}");
                    } else {
                        throw new \Exception('Error al subir el archivo a Google Cloud Storage');
                    }

                } catch (\Exception $e) {
                    // Log del error
                    \Log::error('Error al subir factura a GCS: ' . $e->getMessage());

                    // Fallback: subir al almacenamiento local con la misma estructura
                    try {
                        $year = date('Y');
                        $monthName = date('F');
                        $directoryPath = "{$year}_facturas/{$monthName}";

                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;

                        $invoicePath = $file->storeAs($directoryPath, $fileName, 'public');
                       // Construir URL para almacenamiento local
                        $invoicePath = asset('storage/' . $invoicePath);

                        \Log::info("Factura subida a almacenamiento local como fallback: {$invoicePath}");
                    } catch (\Exception $fallbackError) {
                        \Log::error('Error en fallback local: ' . $fallbackError->getMessage());
                        throw new \Exception('No se pudo subir la factura');
                    }
                }
            }

            // Crear la compra
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'document_type_id' => $request->document_type_id,
                'total_cost' => 0, // Se actualizará después
                'purchase_datetime' => $request->purchase_datetime,
                'status' => $request->status,
                'purchase_invoice' => $invoicePath,
            ]);

            $totalCost = 0;

            // Guardar detalles de la compra
            foreach ($request->details as $detail) {
                $subtotal = $detail['quantity'] * $detail['unit_cost'];
                $totalCost += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_cost' => $detail['unit_cost'],
                    'subtotal' => $subtotal,
                ]);

                $product = Product::find($detail['product_id']);
                $product->quantity += $detail['quantity'];
                $product->save();

                // Actualizar el inventario
                Inventory::create([
                    'product_id' => $detail['product_id'],
                    'type' => 'purchase',
                    'quantity' => $detail['quantity'],
                    'reason' => 'compra',
                    'user_id' => Auth::id(),
                ]);
            }

            // Actualizar el costo total de la compra
            $purchase->update(['total_cost' => $totalCost]);

            // Registrar la transacción financiera
            Transaction::create([
                'type' => 'purchase',
                'amount' => $totalCost,
                'reference_id' => $purchase->id,
                'description' => 'Compra de productos al proveedor ID: ' . $purchase->supplier_id,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('purchases.index')->with('success', 'Compra registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
        $purchase->load(['supplier', 'user', 'documentType', 'purchaseDetails.product']);
        return view('purchase.show', compact('purchase'));
    }



    /**
     * Cancela una compra.
     */
    public function cancel(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            // Revertir stock en inventarios
            foreach ($purchase->purchaseDetails as $detail) {
                Inventory::create([
                    'product_id' => $detail->product_id,
                    'type' => 'ajuste',
                    'quantity' => -$detail->quantity,
                    'reason' => 'cancelación de compra',
                    'user_id' => Auth::id(),
                ]);
            }

            // Cambiar el estado de la compra
            $purchase->update(['status' => 'cancelada']);

            // Registrar la transacción de cancelación
            Transaction::create([
                'type' => 'cancelación',
                'amount' => -$purchase->total_cost,
                'referenced_id' => $purchase->id,
                'description' => 'Cancelación de compra ID: ' . $purchase->id,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('purchases.index')->with('success', 'Compra cancelada y stock ajustado.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
