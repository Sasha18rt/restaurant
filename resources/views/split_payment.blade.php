<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Split Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{url('CSS/customer.css')}}">
    <style>
        .order-checkbox {
            margin-left: 10px;
            transform: scale(1.5);
        }
        .footer-buttons {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            padding: 10px 15px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .total-price {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn-pay-all {
            width: 100%;
        }
        .stripe-button {
            background-color: #6772e5;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1.2em;
            display: none; /* Hide until needed */
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<div class="footer-buttons">
    <div class="total-price">Total: 0 €</div>
    <button id="stripe-button" class="stripe-button" onclick="handlePayment()">Pay Selected</button>
</div>
<div class="header">
    <div class="back-icon" onclick="window.history.back();">&#8592;</div>
    <h2>Split Payment for Table {{ $table->id }}</h2>
    <div class="user-icon"><i class="fas fa-user"></i></div>
</div>
<div class="container">
    <h2>Select Items to Pay</h2>
    <form id="split-payment-form">
        @csrf
        @foreach($table->orders()->whereIn('payment_status', ['unpaid'])->get() as $order)
        @php
            // Calculate the total price including addons
            $totalPrice = $order->price;
            foreach($order->addons as $addon) {
                $totalPrice += $addon->price;
            }
        @endphp
        <div class="order-card">
            <img src="{{ asset('foodimage/' . $order->dish->image) }}" alt="{{ $order->dish->title }}">
            <div class="order-details">
                <div class="order-title">{{ $order->quantity }} x {{ $order->dish->title }}</div>
                <div class="order-status">{{ ucfirst($order->status) }}</div>
                <div class="order-price">{{ number_format($totalPrice, 2) }} €</div>
            </div>
            <input type="checkbox" class="order-checkbox" name="orders[]" value="{{ $order->id }}" data-price="{{ number_format($totalPrice, 2) }}" onchange="updateTotal()">
        </div>
        @endforeach
    </form>
</div>

<script>
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.order-checkbox:checked').forEach(checkbox => {
            total += parseFloat(checkbox.getAttribute('data-price'));
        });
        document.querySelector('.total-price').innerText = 'Total: ' + total.toFixed(2) + ' €';

        if (total > 0) {
            document.getElementById('stripe-button').style.display = 'block';
        } else {
            document.getElementById('stripe-button').style.display = 'none';
        }
    }

    function handlePayment() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(checkbox => checkbox.value);

    if (selectedOrders.length === 0) {
        alert('Please select at least one item to pay.');
        return;
    }

    const form = document.getElementById('split-payment-form');
    const formData = new FormData(form);

    fetch('{{ route('stripe') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            orders: selectedOrders
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        } else if (data.message) {
            alert(data.message);
        } else {
            alert('Failed to create payment session');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{ asset('index.js') }}"></script>
</body>
</html>
