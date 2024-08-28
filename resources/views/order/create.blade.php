<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/e3a49d370f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ url('CSS/table.css') }}">
    <style>
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
        }
        .btn-action, .btn-quantity {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
        }
        .btn-action:hover, .btn-quantity:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Create Order for {{ $tableId === 'takeaway' ? 'Takeout' : 'Table ' . $tableId }}</h1>
</div>
<div class="container content">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('order.selectDishType') }}" method="GET">
        @csrf
        @if($tableId !== 'takeaway')
            <input type="hidden" name="table_id" value="{{ $tableId }}">
        @else
            <input type="hidden" name="table_id" value="">
        @endif

        <button type="submit" class="btn btn-primary btn-plus">+ Add Dish</button>
    </form>

    <div id="order-items" class="order-items">
    @foreach($orders as $order)
        @php
            // Calculate the total price including add-ons
            $totalPrice = $order->price;
        @endphp
        <div class="order-item card">
            <div class="card-body">
                <div class="action-buttons">
                    <h5 class="card-title">{{ $order->dish->title }}</h5>
                    <div>
                        <button type="button" class="btn-action" onclick="editOrder({{ $order->id }})">
                        <i class="fas fa-edit fa-2x"></i>
                        </button>
                        <form action="{{ route('order.removeItem', $order->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action">
                                <i class="fas fa-trash fa-2x"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="card-text">Price per Dish: {{ number_format($totalPrice, 2) }} €</p>
                <p class="card-text">Quantity: 
                    <form action="{{ route('order.updateQuantity', $order->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn-quantity" onclick="decrementQuantity({{ $order->id }})">-</button>
                        <input type="number" name="quantity" value="{{ $order->quantity }}" class="quantity-input" min="1" data-order-id="{{ $order->id }}" onchange="this.form.submit()">
                        <button type="button" class="btn-quantity" onclick="incrementQuantity({{ $order->id }})">+</button>
                    </form>
                </p>
                @if($order->addons->count() > 0)
                    <p class="card-text">Add-ons: 
                        @foreach($order->addons as $addon)
                            {{ $addon->addon_name }} (+{{ number_format($addon->price, 2) }} €){{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </p>
                @endif
                @if($order->comment)
                    <p class="card-text">Comment: {{ $order->comment }}</p>
                @endif
            </div>
        </div>
    @endforeach
</div>

</div>

<form action="{{ route('order.complete') }}" method="POST">
    @csrf
    @if($tableId !== 'takeaway')
        <input type="hidden" name="table_id" value="{{ $tableId }}">
    @else
        <input type="hidden" name="table_id" value="">
    @endif
    <button type="submit" class="btn btn-success" id="submit-order">Complete Order</button>
</form>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    function incrementQuantity(orderId) {
        let input = document.querySelector(`input[data-order-id="${orderId}"]`);
        input.value = parseInt(input.value) + 1;
        input.form.submit();
    }

    function decrementQuantity(orderId) {
        let input = document.querySelector(`input[data-order-id="${orderId}"]`);
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
            input.form.submit();
        }
    }

    function editOrder(orderId) {
        window.location.href = `/order/edit/${orderId}`;
    }
</script>
</body>
</html>
