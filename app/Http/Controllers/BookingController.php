<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Booking;
use App\Events\BookingCreated;


class BookingController extends Controller
{
    /**
     * Get list of all bookings.
     *
     * @api v1
     * @method GET
     * @uri http://localhost/api/bookings/
     *
     * @return 200
     * @return 500
     */
    public function index()
    {

        try {
            $bookings = Booking::with('room', 'user')->get();
            return response()->json($bookings, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created booking in storage.
     *
     * @api v1
     * @method POST
     * @uri http://localhost/api/bookings/
     *
     *
     * @param int|required room_id - The id of the room
     * @param int|required user_id - The id of the user
     * @param string|required check_in_date - The check in date
     * @param string|required check_out_date - The check out date
     * @param float|required total_price - The total price of the booking
     *
     * @return 201
     * @return 500
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required|exists:users,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_price' => 'required|numeric'
        ]);

        try {

            // Check room availability
            if (!$this->isRoomAvailable($validatedData['room_id'], $validatedData['check_in_date'], $validatedData['check_out_date'])) {
                return response()->json(['error' => 'Room is not available for the selected dates'], 400);
            }

            $booking = Booking::create($validatedData);

            event(new BookingCreated($booking));

            return response()->json($booking, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error occurred while creating booking'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }


    private function isRoomAvailable($roomId, $checkInDate, $checkOutDate)
    {
        $existingBookings = Booking::where('room_id', $roomId)
            ->where(function($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                    ->orWhere(function($query) use ($checkInDate, $checkOutDate) {
                        $query->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            })
            ->exists();

        return !$existingBookings;
    }


    /**
     * Display the specified resource.
     *
     * @api v1
     * @method GET
     * @uri http://localhost/api/bookings/{booking_id}
     *
     *
     * @param int|required booking_id - The id of the booking
     *
     * @return 200
     * @return 404
     * @return 500
     */
    public function show(Booking $booking)
    {
        try {
            $booking = $booking->load('room', 'user');
            return response()->json($booking, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Booking not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching booking details', 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update a specified booking.
     *
     * @api v1
     * @method PUT
     * @uri http://localhost/api/bookings/
     *
     *
     * @param int|optional room_id - The id of the room
     * @param int|optional user_id - The id of the user
     * @param string|optional check_in_date - The check in date
     * @param string|optional check_out_date - The check out date
     * @param float|optional total_price - The total price of the booking
     *
     * @return 200
     * @return 422
     * @return 500
     */
    public function update(Request $request, Booking $booking)
    {
        try {

            $booking->update($request->all());
            return response()->json($booking, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating booking', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @api v1
     * @method DELETE
     * @uri http://localhost/api/bookings/{booking_id}
     *
     *
     * @param int|required booking_id - The id of the booking
     *
     * @return 204
     * @return 404
     * @return 500
     */
     public function destroy(Booking $booking)
     {
        try {
            $booking->delete();
            return response()->json('Booking deleted!', 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Booking not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting the booking', 'message' => $e->getMessage()], 500);
        }
     }
}
