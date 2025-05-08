<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use ShaonMajumder\Facades\CacheHelper;
use App\Models\Service;

/**
 * @OA\Tag(
 *     name="Admin API",
 *     description="APIs for managing services and view bookings by admins"
 * )
 * 
 *  @OA\Schema(
 *     schema="Booking",
 *     type="object",
 *     title="Booking",
 *     required={"id", "user_id", "service_id", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=12),
 *     @OA\Property(property="service_id", type="integer", example=3),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-05-09T14:30:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-08T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-08T10:30:00Z"),
 *     @OA\Property(
 *         property="service",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=3),
 *         @OA\Property(property="name", type="string", example="Plumbing"),
 *         @OA\Property(property="price", type="number", format="float", example=200.5)
 *     )
 * )
 */
class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/bookings",
     *     summary="List bookings with pagination",
     *     tags={"Admin Bookings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of bookings",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid pagination parameters"
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
                'message' => 'Bookings retrieved from cache.',
                'data'    => $cached,
            ]);
        }

        $paginator = Booking::with(['service:id,name,category,price,description'])
                            ->select('id', 'name', 'phone_number', 'service_id', 'status', 'schedule_date_time')
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
            'message' => 'Bookings retrieved from database.',
            'data'    => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/admin/bookings/{id}",
     *     summary="Get a specific booking",
     *     tags={"Admin Bookings"},
     *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="Booking UUID",
    *         @OA\Schema(type="string", format="uuid", example="b3c5a1a4-85e1-4b4e-b1e5-bb9f7c19e3ea")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Booking found",
    *         @OA\JsonContent(ref="#/components/schemas/Booking")
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Booking not found"
    *     )
    * )
    */
    public function show($id)
    {
        $booking = Booking::with('service:id,name,category,price,description')->findOrFail($id);
        return response()->json($booking);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
