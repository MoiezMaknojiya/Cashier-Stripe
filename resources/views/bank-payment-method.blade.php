<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
<!-- In your Blade template -->
<script src="https://js.stripe.com/v3/"></script>

<div id="card-element"></div>
<input type="text" id="card-holder-name" placeholder="Cardholder's name">
<button id="card-button" data-secret="{{ $intent->client_secret }}">
    Confirm Bank Account Setup
</button>

<script>
    const StripeID = "{{ $stripe_key }}";
    const stripe = Stripe(StripeID);

    // Note: We don't need to create a 'us_bank_account' element
    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;

    cardButton.addEventListener('click', async (e) => {
        e.preventDefault(); // Prevent default form submission
        const { setupIntent, error } = await stripe.confirmUsBankAccountSetup(
            clientSecret,
            {
                payment_method: {
                    us_bank_account: {
                        routing_number: '110000000',
                        account_number: '000123456789',
                        account_holder_type: 'individual',
                    },
                    billing_details: {
                        name: cardHolderName.value,
                        email: 'jenny@example.com',
                    },
                },
            }
        );

        if (error) {
            // Display "error.message" to the user...
            console.log(error.message);
        } else {
            // The bank account has been verified successfully...
            console.log('Success');
        }
    });
</script>

</x-app-layout>
