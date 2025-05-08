<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use ShaonMajumder\Facades\CacheHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Schema(
 *     schema="ServiceRequest",
 *     type="object",
 *     required={"name", "category", "price", "duration"},
 *     @OA\Property(property="name", type="string", example="AC Repair"),
 *     @OA\Property(property="category", type="string", example="Home Services"),
 *     @OA\Property(property="price", type="number", format="float", example=1500.75),
 *     @OA\Property(property="duration", type="integer", example=90)
 * )
 */
class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/admin/services",
     *     summary="Get a list of services",
     *     tags={"Admin Services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of services",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Services retrieved successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Service")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $services = Service::paginate(10);
            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully.',
                'data' => $services
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching services.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/services",
     *     summary="Create a new service",
     *     tags={"Admin Services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ServiceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service created successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Service"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Service already exists",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A service with the same name and category already exists."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service creation failed."
     *             )
     *         )
     *     )
     * )
     */
    public function store(ServiceRequest $request)
    {
        try {
            $validated = $request->validated();
            $exists = Service::where('name', $validated['name'])
                         ->where('category', $validated['category'])
                         ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'A service with the same name and category already exists.',
                ], Response::HTTP_CONFLICT);
            }
            
            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.',
                'data' => $service
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Failed to create service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service creation failed.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/services/{id}",
     *     summary="Get a specific service by ID",
     *     tags={"Admin Services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service retrieved successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Service"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service not found."
     *             )
     *         )
     *     )
     * )
     */
    public function show(Service $service)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Service retrieved successfully.',
                'data' => $service
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Service not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to retrieve service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving service.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\Put(
     *     path="/api/v1/admin/services/{id}",
     *     summary="Update a specific service",
     *     tags={"Admin Services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ServiceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service updated successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Service"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Another service with the same name and category already exists",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Another service with the same name and category already exists."
     *             )
     *         )
     *     ),
     *
     * 
     * @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service not found."
     *             )
     *         )
     *     )
     * )
    */
    public function update(ServiceRequest $request, Service $service)
    {
        try {
            $validated = $request->validated();
            
            $exists = Service::where('name', $validated['name'])
                            ->where('category', $validated['category'])
                            ->where('id', '!=', $service->id)
                            ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Another service with the same name and category already exists.',
                ], Response::HTTP_CONFLICT);
            }

            $service->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.',
                'data' => $service
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Service not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service update failed.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/v1/admin/services/{id}",
     *     summary="Delete a specific service",
     *     tags={"Admin Services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service deleted successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Service not found."
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(Service $service)
    {
        try {
            $service->delete();
            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Service deletion failed.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
