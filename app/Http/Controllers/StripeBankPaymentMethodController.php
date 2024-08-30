<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StripeBankPaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        // For US Bank Account
        $intent = $user->createSetupIntent(['payment_method_types' => ['us_bank_account']]);
        return view('bank-payment-method', [
            'intent' => $intent,
            'stripe_key' => "pk_test_51PqctP2LyVgVxJCXSqisWbCBdiEQ3UPPdZovOJXYszAmYecbQa9QEUrenii1yaaciO58zbYE6Ar2GtKa89KpSNAR009x98ylqW",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
    }
}
