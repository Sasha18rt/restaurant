<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{url('CSS/customer.css')}}">
</head>
<body>
<div class="header">
<div class="back-icon">
        <a href="/" style="color: white;">
            <i class="fas fa-home"></i>
        </a>
    </div>

    
    <h2>Table {{ $table->id }}</h2>
    
    <div class="user-icon">
        @if (Route::has('login'))
            @auth
                <a href="{{ route('profile.show') }}">
                    <i class="fas fa-user" style="color: white;"></i>
                </a>
            @else
                <a href="{{ route('login') }}">
                    <i class="fas fa-user" style="color: white;"></i>
                </a>
            @endauth
        @endif
    </div>
</div>

<div class="container">
    <h2>Current Orders</h2>
    @foreach($table->orders()
    ->where(function($query) {
        $query->where('payment_status', 'unpaid')
              ->orWhere('status', '!=', 'completed');
    })
    ->get() as $order)
    @php
        // Calculate the total price including addons
        $totalPrice = $order->price;
        foreach($order->addons as $addon) {
            $totalPrice += $addon->price;
        }
    @endphp
    <div class="order-card" onclick="toggleDetails(this)">
    <img src="{{ asset('foodimage/' . $order->dish->image) }}" alt="{{ $order->dish->title }}">
    <div class="order-details">
        <div class="order-title">{{ $order->quantity }} x {{ $order->dish->title }}</div>
        <div class="order-status">{{ ucfirst($order->status) }}</div>
        <div class="order-price">{{ $totalPrice }} €</div>
        @if($order->addons->count() > 0)
            <div class="addons-label">Add-ons:</div>
            <ul class="order-addons">
                @foreach($order->addons as $addon)
                    <li>{{ $addon->addon_name }} (+{{ $addon->price }} €)</li>
                @endforeach
            </ul>
        @endif
        @if($order->payment_status == 'unpaid')
            <div class="payment-status">
                <i class="fas fa-credit-card"></i>
                unpaid
            </div>
           
        @endif
    </div>
</div>

    @endforeach
</div>

<div class="footer-buttons" style="flex-direction: column;">
<form action="{{ route('table.menu', ['id' => $table->id]) }}" method="GET" style="width: 100%;">
    <button type="submit" class="btn btn-order-more">Order More</button>
</form>

    <div style="display: flex; width: 100%;">
        <form action="{{ route('table.split', ['id' => $table->id]) }}" method="GET" style="flex: 1;">
            <button type="submit" class="btn btn-split">Split</button>
        </form>
        <form action="javascript:void(0);" onsubmit="handlePayment()" style="flex: 1;">
    <button type="submit" class="btn btn-pay-all">Pay All</button>
</form>


    </div>
</div>

<script>
    function toggleDetails(element) {
        const addonsLabel = element.querySelector('.addons-label');
        const addons = element.querySelector('.order-addons');
        const comment = element.querySelector('.order-comment');
        if (addonsLabel) {
            $(addonsLabel).slideToggle("slow");
        }
        if (addons) {
            $(addons).slideToggle("slow");
        }
        if (comment) {
            $(comment).slideToggle("slow");
        }
    }


    function handlePayment() {
    fetch('{{ route('table.payAll', ['id' => $table->id]) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        } else if (data.error) {
            alert(data.error);
        } else {
            alert('Failed to create payment session');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}



</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{ asset('index.js') }}"></script>
</body>
</html>
