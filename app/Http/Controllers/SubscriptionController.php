<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Laravel\Cashier\Exceptions\IncompletePayment;

use Laravel\Cashier\Subscription as CashierSubscription;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // // Check if the user already has an active subscription
        // if ($user->subscribed('prod_QZTKqbk4QPmXA2'))
        // {
        //     return response()->json(['message' => 'You already have an active subscription.'], 400);
        // }

        try
        {
            $paymentMethods = $user->paymentMethods();
            $firstPaymentMethod = $paymentMethods->last();
            if($firstPaymentMethod)
            {
                $paymentMethodID = $firstPaymentMethod->id;
                $user->newSubscription("prod_QZTKqbk4QPmXA2","price_1PiKNiDtGoQsuHRvsLKaeNcQ")->create($paymentMethodID);
            }
        }
        catch (IncompletePayment $exception) 
        {
            // Handle the incomplete payment exception
            return response()->json(['message' => 'Payment incomplete.'], 400);
        }
        return response()->json(['message' => 'Subscription created successfully.']);
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
        
        $user->subscription('prod_QZQBs0O5uf6yAJ')->resume();
        
        dd('dd');


        $subscription = $user->subscription('prod_QZQBs0O5uf6yAJ');

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


    public function duplicate(Request $request)
    {
        $user = auth()->user();
        // Fetch all subscriptions for the specified user
        $subscriptions = CashierSubscription::where('user_id', $user->id)->get();
        $subscriptions = $subscriptions->slice(0, -1);
        //
        foreach($subscriptions as $subscription)
        {
            echo "Subscription ID: " . $subscription->stripe_id . "\n";
            echo "Created At: " . $subscription->created_at . "\n";

            // Fetch all invoices for the subscription, sorted by creation date
            $invoices = $subscription->invoices();

            foreach ($invoices as $invoice)
            {
                if ($invoice->payment_intent)
                {
                    $payment_id = $invoice->payment_intent;
                    // $user->refund($payment_id);
                    
                    echo "invoice ID: " . $payment_id . "\n";
                }
            }
            echo "\n";

            // Cancel the subscription
            // $subscription->cancel(); // Cancels at the end of the billing period

            // Uncomment the following line to cancel immediately
            // $subscription->cancelNow();

            // $subscription->items()->delete();
            // $subscription->delete();
        }
        return response()->json(['message' => 'Duplicate subscriptions handled successfully.']);
    }

}
