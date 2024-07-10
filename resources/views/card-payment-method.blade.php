<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <input id="card-holder-name" type="text">
    <!-- Stripe Elements Placeholder -->
    <div id="card-element"></div>
    <button id="card-button" data-secret="{{ $intent->client_secret }}">Update Payment Method</button>
    <!-- Include Stripe.js library -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (function() {
            let stripe_id = "{{ $stripe_key }}";

            let stripe = Stripe(stripe_id);

            let elements = stripe.elements();
            let cardElement = elements.create('card');

            cardElement.mount('#card-element');

            let cardHolderName = document.getElementById('card-holder-name');
            let cardButton = document.getElementById('card-button');
            let clientSecret = cardButton.dataset.secret;

            cardButton.addEventListener('click', async (e) => {
                let { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );
                if (error) {
                    // Display "error.message" to the user...
                    console.log(error.message);
                } else {
                    // The card has been verified successfully...
                    console.log('Success');
                }
            });
        })();
    </script>
</x-app-layout>