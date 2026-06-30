<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\CreateSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * List all suppliers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();

        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('tax_id', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->paginate($request->get('per_page', 15));

        return response()->json(SupplierResource::collection($suppliers));
    }

    /**
     * Create supplier
     */
    public function store(CreateSupplierRequest $request): JsonResponse
    {
        try {
            $supplier = Supplier::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                ...$request->validated(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully',
                'data' => new SupplierResource($supplier),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create supplier',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get supplier details
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json(new SupplierResource($supplier));
    }

    /**
     * Update supplier
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        try {
            $supplier->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'data' => new SupplierResource($supplier),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete supplier
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        try {
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
