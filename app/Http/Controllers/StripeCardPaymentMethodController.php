<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StripeCardPaymentMethodController extends Controller
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
        $intent = $user->createSetupIntent();

        // For US Bank Account
        // $intent = $user->createSetupIntent(['payment_method_types' => ['us_bank_account']]);

        return view('card-payment-method', [
            'intent' => $intent,
            'stripe_key' => "pk_test_ynOXQ84MGf9bGnRpukVxtz4D00Q2RbpzLy",
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
    public function show(Request $request)
    {
        $user = auth()->user();
        $paymentMethods = $user->paymentMethods();
        $firstPaymentMethod = $paymentMethods->last();
        if($firstPaymentMethod)
        {
            $paymentMethodId = $firstPaymentMethod->id;
            $user->updateDefaultPaymentMethod($paymentMethodId);
        }
        else
        {
            // Handle the case where there are no payment methods
            $paymentMethodId = null;
        }
        return $paymentMethodId;
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
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $user->deletePaymentMethods();

        // $paymentMethods = $user->paymentMethods();
        // $firstPaymentMethod = $paymentMethods->first();
        // if ($firstPaymentMethod)
        // {
        //     $paymentMethodId = $firstPaymentMethod->id;
        //     $paymentMethod = $user->findPaymentMethod($paymentMethodId);
        //     $paymentMethod->delete();
        // }
    }
}
