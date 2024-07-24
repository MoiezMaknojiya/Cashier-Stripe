<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StripeCustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        try {
            // Dummy customer data
            $customerData = [
                'address' => [
                    'line1' => '10100 North Lamar Boulevard',
                    'city' => 'Austin',
                    'postal_code' => '78753 ',
                    'state' => 'TX',
                    'country' => 'US',
                ],
                'description' => 'Test customer for demonstration purposes',
                'email' => 'devteam@gmail.com',
                'metadata' => [
                    'custom_key' => 'value',
                    'other_key' => 'other_value',
                ],
                'name' => 'Dev Team',
                // 'payment_method' => 'pm_card_visa', // Example PaymentMethod ID from Stripe (replace with actual ID)
                'phone' => '+9876543210',
                'shipping' => [
                    'address' => [
                        'line1' => '10100 North Lamar Boulevard',
                        'city' => 'Austin',
                        'postal_code' => '78753 ',
                        'state' => 'TX',
                        'country' => 'US',
                    ],
                    'name' => 'Dev Team',
                    'phone' => '+9876543210',
                ],
            ];

            // Create the customer using Laravel Cashier
            $stripeCustomerResponse = $user->createAsStripeCustomer($customerData);

            // Optionally, handle response or notify the user
            // Example: return response()->json(['message' => 'Dummy customer created successfully', 'customer' => $stripeCustomerResponse]);

            return response()->json(['message' => 'Dummy customer created successfully', 'customer' => $stripeCustomerResponse]);
        } catch (InvalidRequestException $e) {
            // Handle Stripe API errors
            // Example: return response()->json(['error' => 'Failed to create dummy customer: ' . $e->getMessage()], 500);

            return response()->json(['error' => 'Failed to create dummy customer: ' . $e->getMessage()], 500);
        }
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
        dd($user->asStripeCustomer());
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
        $user = auth()->user(); // Retrieve authenticated user

        $options = [
            'address' => [
                'line1' => '10100 North Lamar Boulevard',
                'city' => 'Austin',
                'postal_code' => '78753 ',
                'state' => 'TX',
                'country' => 'US',
            ],
            'description' => 'Updated customer description',
            'email' => 'devteam@gmail.com',
            'metadata' => [
                'custom_key' => 'new_value',
                'other_key' => 'updated_value',
            ],
            'name' => 'Dev Team', // Customer's name
            'phone' => '+9876543210', // Customer's phone number
            'shipping' => [
                'address' => [
                    'line1' => '10100 North Lamar Boulevard',
                    'city' => 'Austin',
                    'postal_code' => '78753 ',
                    'state' => 'TX',
                    'country' => 'US',
                ],
                'name' => 'Dev Team', // Shipping recipient's name
                'phone' => '+9876543210', // Shipping recipient's phone number
            ],
        ];

        try {
            // Update the customer using Laravel Cashier
            $stripeCustomerResponse = $user->updateStripeCustomer($options);

            // Optionally handle the response or redirect with a success message
            // return redirect()->back()->with('success', 'Customer information updated successfully.');
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the update process
            // Log or display error messages as needed
            // return redirect()->back()->with('error', 'Failed to update customer information.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        $user->asStripeCustomer()->delete();
        // Set Stripe ID Null 
        $user->stripe_id = null;
        $user->save();
    }
}
