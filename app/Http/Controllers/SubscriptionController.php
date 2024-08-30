<?php

namespace App\Http\Controllers;
use App\Models\SubscriptionItems;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\SubscriptionItem;
use Laravel\Cashier\Subscription as CashierSubscription;
use Stripe\Stripe;
use Stripe\Subscription;

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
                // $user->newSubscription("prod_QjwHZJQh4tnHm6","price_1PsSOe2LyVgVxJCXx4mE5Lh5")->create($paymentMethodID);
                // $user->newSubscription('prod_QjwHURqX3lCtE5')->price('price_1PsSOp2LyVgVxJCXqTmedNVW')->create($paymentMethodID)->noProrate()->updateQuantity(3);
                
                // For multiple products in single subscription
                $firstPaymentMethod = $paymentMethods->last();
                $paymentMethodID = $firstPaymentMethod->id;
                $user->newSubscription('default', [
                    'price_1PsSev2LyVgVxJCXE68pvkcn',
                    'price_1PsSel2LyVgVxJCXqki117M7',
                ])->create($paymentMethodID);
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
    public function update(Request $request)
    {
        $user = auth()->user();

        // Set your Stripe secret key | idr islie dala q ky env se nahi utha rha tha
        Stripe::setApiKey('sk_test_51PqctP2LyVgVxJCXt52p6oodEph4aLYISbqKAMX17yonjgYNfIITZgU4bXDjGKb4JHsI28bv7sihRYdLWN12bZ9z000FK6tW3z');

        // Retrieve the user's existing subscription ID from the database
        $subscriptionId = $user->subscription('default')->stripe_id;

        try {
            $newPriceId = 'price_1PsSel2LyVgVxJCXqki117M7'; 
            $quantity = 2;

            // Retrieve the subscription from Stripe
            $subscription = Subscription::retrieve($subscriptionId);

            // Find the existing subscription item with the given price ID
            $subscriptionItem = collect($subscription->items->data)->firstWhere('price.id', $newPriceId);

            if ($subscriptionItem) {
                // Update the existing subscription item with the new quantity
                $updatedItem = \Stripe\SubscriptionItem::update(
                    $subscriptionItem->id,
                    [
                        'quantity' => $subscriptionItem->quantity + $quantity,
                        'proration_behavior' => 'create_prorations',
                    ]
                );

                // Manually update your database
                $subscriptionItemModel = SubscriptionItem::where('subscription_id', $subscriptionId)
                    ->where('stripe_price', $newPriceId)
                    ->first();

                if ($subscriptionItemModel) {
                    $subscriptionItemModel->quantity = $updatedItem->quantity;
                    $subscriptionItemModel->save();
                }

                return response()->json(['message' => 'Subscription item updated successfully.', 'item' => $updatedItem]);
            } else {
                // Create a new subscription item if it doesn't exist
                $newItem = \Stripe\SubscriptionItem::create([
                    'subscription' => $subscriptionId,
                    'price' => $newPriceId,
                    'quantity' => $quantity,
                    'proration_behavior' => 'create_prorations',
                ]);

                // Save to your database
                SubscriptionItem::create([
                    'subscription_id' => $subscriptionId,
                    'stripe_price' => $newPriceId,
                    'quantity' => $quantity,
                ]);

                return response()->json(['message' => 'New subscription item added successfully.', 'item' => $newItem]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // $user = auth()->user();
        // // Set your Stripe secret key
        // // Stripe::setApiKey(env('STRIPE_SECRET'));
        // Stripe::setApiKey('sk_test_51PqctP2LyVgVxJCXt52p6oodEph4aLYISbqKAMX17yonjgYNfIITZgU4bXDjGKb4JHsI28bv7sihRYdLWN12bZ9z000FK6tW3z');
        // // Retrieve the user's existing subscription ID
        // $subscriptionId = $user->subscription('default')->id;
        
        
        // try {
        //     $newPriceId = 'price_1PsSel2LyVgVxJCXqki117M7'; 
        //     $quantity = 2;
        //     $subscriptionItem = SubscriptionItems::where('subscription_id', $subscriptionId)->where('stripe_price', $newPriceId)->first();
        //     dd($subscriptionItem);
        //     if ($subscriptionItem) {
        //         // Update the existing subscription item if found
        //         $updatedItem = $subscriptionItem->updateStripeSubscriptionItem([
        //             'quantity' => $subscriptionItem->quantity + $quantity,
        //             'proration_behavior' => 'create_prorations',
        //         ]);
        //         return response()->json(['message' => 'Subscription item updated successfully.', 'item' => $updatedItem]);
        //     } else {
        //         // Create a new subscription item if it doesn't exist
        //         $newItem = SubscriptionItem::create([
        //             'subscription' => $subscriptionId,
        //             'price' => $newPriceId,
        //             'quantity' => $quantity,
        //             'proration_behavior' => 'create_prorations',
        //         ]);
        //         return response()->json(['message' => 'New subscription item added successfully.', 'item' => $newItem]);
        //     }
        // } 
        // catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
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
