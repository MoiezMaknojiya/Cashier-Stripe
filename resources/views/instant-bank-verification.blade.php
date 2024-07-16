<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <script src="https://js.stripe.com/v3/"></script>

    <form id="payment-method-form">
        <input type="text" id="account-holder-name-field" placeholder="Account Holder Name" required>
        <input type="email" id="email-field" placeholder="Email" required>
        <button type="submit">Submit</button>
    </form>

    <form id="confirmation-form" style="display:none;">
        <button type="submit">Confirm Bank Account Setup</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe("{{ $stripe_key }}");
            const clientSecret = "{{ $intent->client_secret }}";

            const paymentMethodForm = document.getElementById('payment-method-form');
            const confirmationForm = document.getElementById('confirmation-form');

            paymentMethodForm.addEventListener('submit', (ev) => {
                ev.preventDefault();
                const accountHolderNameField = document.getElementById('account-holder-name-field');
                const emailField = document.getElementById('email-field');

                // Open the instant verification dialog.
                stripe.collectBankAccountForSetup({
                    clientSecret: clientSecret,
                    params: {
                        payment_method_type: 'us_bank_account',
                        payment_method_data: {
                            billing_details: {
                                name: accountHolderNameField.value,
                                email: emailField.value,
                            },
                        },
                    },
                    expand: ['payment_method'],
                }).then(({setupIntent, error}) => {
                    if (error) {
                        console.error(error.message);
                    } else if (setupIntent.status === 'requires_payment_method') {
                        console.log("Customer canceled the hosted verification modal.");
                    } else if (setupIntent.status === 'requires_confirmation') {
                        console.log("Bank account collected. Displaying confirmation form.");
                        confirmationForm.style.display = 'block';
                    }
                });
            });

            confirmationForm.addEventListener('submit', (ev) => {
                ev.preventDefault();
                stripe.confirmUsBankAccountSetup(clientSecret)
                .then(({setupIntent, error}) => {
                    if (error) {
                        console.error(error.message);
                    } else if (setupIntent.status === "requires_payment_method") {
                        console.log("Confirmation failed. Attempt again with a different payment method.");
                    } else if (setupIntent.status === "succeeded") {
                        console.log("Confirmation succeeded! The account is now saved.");
                    } else if (setupIntent.next_action?.type === "verify_with_microdeposits") {
                        console.log("The account needs to be verified via microdeposits.");
                    }
                });
            });
        });
    </script>

</x-app-layout>
