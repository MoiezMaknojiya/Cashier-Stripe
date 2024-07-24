<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $paymentMethods = $user->paymentMethods();
        $firstPaymentMethod = $paymentMethods->last();
        if($firstPaymentMethod)
        {
            $paymentMethodID = $firstPaymentMethod->id;
            $user->newSubscription("prod_QWuF2W5jibyEMy","price_1PfqRODtGoQsuHRvkyfaVgye")->create($paymentMethodID);
            return "Success";
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

    /**
     * Remove the specified resource from storage.
     */
    public function pause(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function resume(string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function retryPayment(Request $request)
    {
        $stripe = new StripeClient(config('cashier.secret'));
        $user = auth()->user();
        
        $user->subscription('prod_QWuF2W5jibyEMy')->resume();
        
        dd('dd');


        $subscription = $user->subscription('prod_QWuF2W5jibyEMy');

        // Ensure the subscription exists
        if (!$subscription)
        {
            dd('Subscription not found.');
        }

        // Ensure the subscription has a latest payment
        if (!$subscription->latestPayment()) {
            dd('No payment found for the subscription.');
        }

        $paymentId = $subscription->latestPayment()->id;

        try {
            // Retrieve the payment intent
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentId);

            $paymentMethods = $user->paymentMethods();
            $firstPaymentMethod = $paymentMethods->last();

            if (!$firstPaymentMethod) {
               dd('No default payment method found.');
            }

            $paymentMethodId = $firstPaymentMethod->id;


            // Update the payment intent with the user's default payment method
            $paymentIntent = $stripe->paymentIntents->update($paymentId, [
                'payment_method' => $paymentMethodId,
            ]);

            // Confirm the payment intent
            $paymentIntent = $stripe->paymentIntents->confirm($paymentId);

            if ($paymentIntent->status === 'succeeded')
            {
               dd('Payment confirmed successfully.');
            }
            else
            {
                dd('Payment could not be confirmed. Please try again.');
            }
        } 
        catch (IncompletePayment $exception)
        {
            dd('Payment could not be confirmed. Please try again.');
          
        } 
        catch (\Exception $e)
        {
            dd('An error occurred: ');
        }
        
        // if ($subscription->hasIncompletePayment()) {
        //     $paymentId = $subscription->latestPayment()->id;
        //     return redirect()->route('cashier.payment', $paymentId)
        //                      ->with('status', 'Please confirm your payment.');
        // }


        // $paymentMethods = $user->paymentMethods();
        // $paymentMethod = $paymentMethods->last();
        // if($paymentMethod)
        // {
        //     $paymentMethodID = $paymentMethod->id;
        // }

        // dd($user->invoice());

        // $user->subscription('prod_QWpiGAZsTEf8ht')->updateStripeSubscription([
        //     'default_payment_method' => $paymentMethodID,
        // ]);
        // // Retry the latest invoice
        // $user->invoice()->pay();
    }
}
