<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StripePaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // $paymentMethods = $user->paymentMethods();

        // dd($paymentMethods);

        // For Card
        // $intent = $user->createSetupIntent();

        // Create the SetupIntent with us_bank_account as an allowed payment method type
        $intent = $user->createSetupIntent([
            'payment_method_types' => ['us_bank_account'],
        ]);


        return view('bank-payment-method', [
            'intent' => $intent,
            'stripe_id' => "pk_test_ynOXQ84MGf9bGnRpukVxtz4D00Q2RbpzLy",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
