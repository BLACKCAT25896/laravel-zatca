<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Http\Requests\CreateBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use App\Http\Resources\BusinessResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /**
     * List all businesses
     */
    public function index(Request $request): JsonResponse
    {
        $query = Business::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('tax_id', 'like', '%' . $request->search . '%');
        }

        $businesses = $query->paginate($request->get('per_page', 15));

        return response()->json(BusinessResource::collection($businesses));
    }

    /**
     * Create business
     */
    public function store(CreateBusinessRequest $request): JsonResponse
    {
        try {
            $business = Business::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                ...$request->validated(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Business created successfully',
                'data' => new BusinessResource($business),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create business',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get business details
     */
    public function show(Business $business): JsonResponse
    {
        return response()->json(new BusinessResource($business));
    }

    /**
     * Update business
     */
    public function update(UpdateBusinessRequest $request, Business $business): JsonResponse
    {
        try {
            $business->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Business updated successfully',
                'data' => new BusinessResource($business),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update business',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete business
     */
    public function destroy(Business $business): JsonResponse
    {
        try {
            $business->delete();

            return response()->json([
                'success' => true,
                'message' => 'Business deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete business',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
