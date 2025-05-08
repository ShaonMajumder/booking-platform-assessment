<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use ShaonMajumder\Facades\CacheHelper;
use Illuminate\Support\Facades\DB;
use App\Notifications\BookingConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     summary="Create a new service booking",
     *     description="This endpoint allows customers to book a service by providing their name, phone number, and service ID.",
     *     operationId="storeBooking",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"service_id", "name", "phone", "schedule_date"},
     *                 @OA\Property(property="service_id", type="string", format="uuid", description="Service UUID"),
     *                 @OA\Property(property="name", type="string", description="Customer name"),
     *                 @OA\Property(property="phone", type="string", description="Customer phone number"),
     *                 @OA\Property(property="schedule_date", type="string", format="date-time", description="Scheduled date and time for service")
     *             ),
     *             example={
     *                 "service_id": "028c9fb6-ebfd-4003-ad5c-681ff37263d5",
     *                 "name": "Shaon",
     *                 "phone": "+8801832540116",
     *                 "schedule_date": "2025-05-09T12:51:22.653Z"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="booking_id", type="string", format="uuid", description="Booking ID"),
     *             @OA\Property(property="status", type="string", description="Booking status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'schedule_date' => 'required|date|after:today',
        ]);

        DB::beginTransaction();

        try {

            $booking = Booking::create([
                'service_id' => $validated['service_id'],
                'name' => $validated['name'],
                'phone_number' => $validated['phone'],
                'schedule_date_time' => Carbon::parse($validated['schedule_date'])->format('Y-m-d H:i:s'),
                'status' => BookingStatus::PENDING,
            ]);
            $booking->load('service');

            DB::commit();

            Notification::route('mail', 'smazoomder@gmail.com')
                ->notify(new BookingConfirmationNotification($booking));
	
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully.',
                'data' => [
                    'booking_id' => $booking->id,
                    'name' => $booking->name,
                    'phone_number' => $booking->phone_number,
                    'service_id' => $booking->service_id,
                    'booking_date' => $booking->schedule_date_time,
                    'status' => BookingStatus::label($booking->status)
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Booking creation failed'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/{bookingId}",
     *     summary="Retrieve the status of a booking",
     *     description="This endpoint allows customers to check the status of their booking using a unique booking ID.",
     *     operationId="getBookingStatus",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="bookingId",
     *         in="path",
     *         required=true,
     *         description="UUID of the booking",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking status retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking retrieved from cache."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="booking_id", type="string", format="uuid", example="e183b1a8-c0b3-41a2-be77-4b16a9670c01"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(
     *                     property="service",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="028c9fb6-ebfd-4003-ad5c-681ff37263d5"),
     *                     @OA\Property(property="name", type="string", example="Cleaning Service"),
     *                     @OA\Property(property="category", type="string", example="Cleaning"),
     *                     @OA\Property(property="price", type="string", example="100.00"),
     *                     @OA\Property(property="description", type="string", example="Cleaning description goes here.")
     *                 ),
     *                 @OA\Property(property="schedule_date_time", type="string", format="datetime", example="2025-05-09 12:51:22")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Booking not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function show($bookingId)
    {
        $cacheKey = CacheHelper::getCacheKey("booking-status-{$bookingId}");
        $cached = CacheHelper::getCache($cacheKey);

        if ($cached) {
            return response()->json([
                'success' => true,
                'message' => 'Booking retrieved from cache.',
                'data'    => $cached,
            ]);
        }

        $booking = Booking::with('service')->find($bookingId);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $service = $booking->service;

        $data = [
            'booking_id'         => $booking->id,
            'status'             => BookingStatus::label($booking->status),
            'service'            => $service ? [
                'id'          => $service->id,
                'name'        => $service->name,
                'category'    => $service->category,
                'price'       => $service->price,
                'description' => $service->description,
            ] : null,
            'schedule_date_time' => $booking->schedule_date_time,
        ];

        CacheHelper::setCache($cacheKey, $data, 60);

        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved from db.',
            'data'    => $data,
        ]);
    }

}
