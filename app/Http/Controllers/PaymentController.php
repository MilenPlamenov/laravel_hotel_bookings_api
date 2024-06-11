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
        return Payment::with('booking')->get();

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
        return $payment->load('booking');

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
        $payment->update($request->all());
        return response()->json($payment, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }
}
