<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Room;

class RoomController extends Controller
{

    /**
     * Get list of all rooms.
     *
     * @api v1
     * @method GET
     * @uri http://localhost/api/rooms/
     *
     * @return 200
     * @return 500
     */
    public function index()
    {

        try {
            $rooms = Room::all();
            return response()->json($rooms, 200);
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
     * Store a newly created room.
     *
     * @api v1
     * @method POST
     * @uri http://localhost/api/rooms/
     *
     *
     * @param string|required number - The number of the room
     * @param string|required type - The type of the room
     * @param float|required price_per_night - The price of the room for 1 night
     * @param string|required status - The room status (can be available|booked|Available|Booked)
     *
     * @return 201
     * @return 500
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'number' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'price_per_night' => 'required|numeric',
            'status' => 'required|in:available,booked,Available,Booked'
        ]);

        try {
            $room = Room::create($validatedData);
            return response()->json($room, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error occurred while creating room' . $e], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Display information about certain room.
     *
     * @api v1
     * @method GET
     * @uri http://localhost/api/rooms/{room_id}
     *
     *
     * @param int|required room_id - The id of the room
     *
     * @return 200
     * @return 404
     * @return 500
     */
    public function show(Room $room)
    {
        try {
            return response()->json($room, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
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
     * Update the information about certain room.
     *
     * @api v1
     * @method PUT
     * @uri http://localhost/api/rooms/{room_id}
     *
     *
     * @param int|required room_id - The id of the room
     * @param string|optional number - The number of the room
     * @param string|optional type - The type of the room
     * @param float|optional price_per_night - The price of the room for 1 night
     * @param string|optional status - The room status (can be available|booked|Available|Booked)
     *
     * @return 200
     * @return 404
     * @return 422
     * @return 500
     */
    public function update(Request $request, Room $room)
    {

        $validatedData = $request->validate([
            'number' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'price_per_night' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|in:available,booked,Available,Booked'
        ]);

        try {
            $room->update($validatedData);
            return response()->json($room, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Delete specific room.
     *
     * @api v1
     * @method DELETE
     * @uri http://localhost/api/rooms/{room_id}
     *
     *
     * @param int|required room_id - The id of the room
     *
     * @return 204
     * @return 500
     */
    public function destroy(Room $room)
    {

        try {
            $room->delete();
            return response()->json('Room deleted successfully', 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }

    }
}
