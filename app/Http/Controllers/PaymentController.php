<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $payments = Payment::with('booking')->get();
            return response()->json($payments, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching payments', 'message' => $e->getMessage()], 500);
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
     * Store a newly created payment in storage.
     *
     * @api v1
     * @method POST
     * @uri http://localhost/api/payments/
     *
     *
     * @param int|required booking_id - The id of the booking
     * @param float|required amount - The amount of the payment
     * @param string|required payment_date - The date of the payment
     * @param string|required status - The status of the payment (can be pending,completed,failed,Pending,Completed,Failed)
     *
     * @return 201
     * @return 500
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,completed,failed,Pending,Completed,Failed'
        ]);

        try {
            $payment = Payment::create($validatedData);
            return response()->json($payment, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error occurred while creating payment'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        try {
            $payment = $payment->load('booking');
            return response()->json($payment, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching payment details', 'message' => $e->getMessage()], 500);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        try {
            $validatedData = $request->validate([
                'amount' => 'sometimes|required|numeric',
                'payment_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|in:pending,completed,failed',
            ]);

            $payment->update($validatedData);
            return response()->json($payment, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation error', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating payment', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();
            return response()->json('Payment deleted!', 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting payment', 'message' => $e->getMessage()], 500);
        }
    }
}
