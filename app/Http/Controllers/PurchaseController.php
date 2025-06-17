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
      /*   dd($request->all()); */



      $request->merge([
        'details' => json_decode($request->input('details'), true)
        ]);

        dd($request->details);

        /* dd($request->input('details')); */
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'document_type_id' => 'required|exists:document_types,id',
            'purchase_datetime' => 'required|date',
            'purchase_invoice' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
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
                $invoicePath = $request->file('purchase_invoice')->store('invoices', 'public');
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
