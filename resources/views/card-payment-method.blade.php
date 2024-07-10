<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<!-- Include Stripe.js library -->
<script src="https://js.stripe.com/v3/"></script>


<input id="card-holder-name" type="text">
 
<!-- Stripe Elements Placeholder -->
<div id="card-element"></div>
 
<button id="card-button" data-secret="{{ $intent->client_secret }}">
    Update Payment Method
</button>

<script src="https://js.stripe.com/v3/"></script>
 
<script>

    const StripeID = "{{ $stripe_id }}";

    const stripe = Stripe(StripeID);
 
    const elements = stripe.elements();
    const cardElement = elements.create('card');
 
    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    
    cardButton.addEventListener('click', async (e) => {
        const { setupIntent, error } = await stripe.confirmCardSetup(
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
</script>


</x-app-layout>
