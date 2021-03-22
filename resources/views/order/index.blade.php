<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title> PayPal Smart Payment Buttons Integration | Server Demo </title>
</head>

<body>
    <div id="paypal-button-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
    </script>
    <script
        src="https://www.paypal.com/sdk/js?client-id=AYIYS4SnBiS6DdMmXdlsCkGu-2A6ikg_QlHPe86vMPQZr4Inqup-lPLd0OwIb6pib6dd7gz9N84W1kSM">
    </script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    application_context: {
                        brand_name : 'JIW',
                        user_action : 'PAY_NOW',
                        shipping_preference: 'NO_SHIPPING'
                    },
                    purchase_units: [{
                        amount: {
                            value: '0.01'
                        }
                    }],
                });
            },
            onApprove: function(data, actions) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // This function captures the funds from the transaction.
                return actions.order.capture()
                    .then(function(details) {
                        if(details.status === 'COMPLETED') {
                            return fetch('paypal-capture-payment', {
                                method: 'POST',
                                headers: {
                                    'content-type': 'application/json',
                                    'Accept': "application/json, text-plain, */*",
                                    'X-Requested-With': "XMLHttpRequest",
                                    'X-CSRF-TOKEN': token
                                },
                                body: JSON.stringify({
                                    paypal_id: details.id,
                                    paypal_order_id: data.orderID,
                                    status: details.status,
                                    customer_email: details.payer.email_address,
                                    customer_name: details.payer.name.given_name + ' ' + details.payer.name.surname,
                                    amount: details.purchase_units[0].amount.value,
                                    currency_code: details.purchase_units[0].amount.currency_code,
                                })
                            })
                            .then(status)
                            .then(function(response){
                                // redirect to the completed page if paid
                                // window.location.href = '/pay-success';
                                alert('success');
                            })
                            .catch(function(error) {
                                // redirect to failed page if internal error occurs
                                // window.location.href = '/pay-failed?reason=internalFailure';
                                console.log(error);
                            });
                        } else if (details.status === 'INSTRUMENT_DECLINED') {
                            // Recoverable state, per:
                            // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                            return actions.restart();
                        } else {
                            // window.location.href = '/pay-failed?reason=failedToCapture';
                            console.log('failed to capture');
                        }
                });
            },
        }).render('#paypal-button-container');

        function status(res) {
            if (!res.ok) {
                throw new Error(res.statusText);
            }
            return res;
        }
    </script>
</body>

</html>
