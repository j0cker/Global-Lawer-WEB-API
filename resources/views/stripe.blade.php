<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>Stripe Demo</title>
 
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
 
        
    </head>
    <body>
 
        <h1>Test Book</h1>
 
        <h3>$500.00</h3>
 
        <form action="/make-payment" method="POST">
            {{ csrf_field() }}
        <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{{ config('services.stripe.key') }}"
            data-amount="500"
            data-name="Demo Book"
            data-description="This is good start up book."
            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
            data-locale="auto">
        </script>
        </form>
    </body>
</html>