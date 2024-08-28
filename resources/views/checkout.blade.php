<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('CSS/customer.css') }}">
    <style>
        .dish-details {
            text-align: center;
            margin-bottom: 20px;
        }
        .dish-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .dish-title {
            margin-top: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .fixed-bottom-button {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .fixed-bottom-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<div class="header"><h1>Checkout</h1></div>
<div class="container my-4">
    <div class="dish-details">
        <img src="{{ asset('foodimage/' . $dish->image) }}" alt="{{ $dish->title }}" class="dish-image">
        <div class="dish-title">{{ $dish->title }}</div>
    </div>

    <form id="checkoutForm" action="{{ route('stripe.singleItem') }}" method="POST">
        @csrf
        <input type="hidden" name="dish_id" value="{{ $dishId }}">
        <input type="hidden" name="table_id" value="{{ $tableId }}">
        <div class="addons-section">
            <label for="addons" class="form-label">Add-ons:</label>
            <div id="addons" class="form-group">
                @foreach($add_ons as $addon)
                    <div class="form-check">
                        <input class="form-check-input addon-checkbox" type="checkbox" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" name="addons[]"
                               data-price="{{ $addon->price }}">
                        <label class="form-check-label" for="addon_{{ $addon->id }}">{{ $addon->addon_name }} (+{{ number_format($addon->price, 2) }} €)</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required value="1">
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comment:</label>
            <textarea id="comment" name="comment" class="form-control"></textarea>
        </div>

        <button type="button" class="fixed-bottom-button" id="payButton">
            Pay: <span id="totalPrice">0.00</span> €
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const basePrice = {{ $dish->price }};
        const quantityInput = document.getElementById('quantity');
        const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
        const totalPriceElement = document.getElementById('totalPrice');
        const payButton = document.getElementById('payButton');
        const checkoutForm = document.getElementById('checkoutForm');

        function calculateTotalPrice() {
            let total = basePrice;
            addonCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.getAttribute('data-price'));
                }
            });
            total *= parseInt(quantityInput.value);
            totalPriceElement.textContent = total.toFixed(2);
        }

        quantityInput.addEventListener('input', calculateTotalPrice);
        addonCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', calculateTotalPrice);
        });

        // Initial calculation
        calculateTotalPrice();

        payButton.addEventListener('click', function () {
            checkoutForm.submit();
        });
    });
</script>
</body>
</html>
