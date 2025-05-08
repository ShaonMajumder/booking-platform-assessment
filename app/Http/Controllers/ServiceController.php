<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use ShaonMajumder\Facades\CacheHelper;

/**
 * @OA\Info(
 *     title="Service Booking API",
 *     version="1.0.0",
 *     description="A Laravel-based API for booking services.",
 *     @OA\Contact(
 *         email="smazoomder@gmail.com"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Service",
 *     type="object",
 *     required={"id", "name", "category", "price", "description"},
 *     @OA\Property(property="id", type="integer", description="Service ID"),
 *     @OA\Property(property="name", type="string", description="Service name"),
 *     @OA\Property(property="category", type="string", description="Service category"),
 *     @OA\Property(property="price", type="number", format="float", description="Service price"),
 *     @OA\Property(property="description", type="string", description="Service description")
 * )
 */
class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Get a list of available services",
     *     description="Retrieves a paginated list of services with optional pagination query parameters. The results are cached for performance.",
     *     operationId="getServices",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number for pagination.",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="The number of services per page.",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of services",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Services retrieved from cache."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/api/services?page=1"),
     *                 @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/api/services?page=2"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://127.0.0.1:8000/api/services?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Service")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error for pagination parameters",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid pagination parameters.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0 || $page <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pagination parameters.',
            ], 422);
        }

        $cacheKey = CacheHelper::getCacheKey("services-v1:page:{$page}:per_page:{$perPage}");
        $cached = CacheHelper::getCache($cacheKey);

        if ($cached) {
            return response()->json([
                'success' => true,
                'message' => 'Services retrieved from cache.',
                'data'    => $cached,
            ]);
        }

        $paginator = Service::select('id', 'name', 'category', 'price', 'description')
            ->paginate($perPage);

        $data = [
            'current_page'     => $paginator->currentPage(),
            'per_page'         => $paginator->perPage(),
            'total'            => $paginator->total(),
            'first_page_url'   => $paginator->url(1),
            'last_page_url'    => $paginator->url($paginator->lastPage()),
            'next_page_url'    => $paginator->nextPageUrl(),
            'prev_page_url'    => $paginator->previousPageUrl(),
            'data'             => $paginator->items(),
        ];

        CacheHelper::setCache($cacheKey, $data, 60);

        return response()->json([
            'success' => true,
            'message' => 'Services retrieved from database.',
            'data'    => $data,
        ]);
    }
}
