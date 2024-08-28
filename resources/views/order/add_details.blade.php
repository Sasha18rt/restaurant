<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('CSS/table.css') }}">
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
    </style>
</head>
<body>
<div class="header"><h1>Add Details</h1></div>
<div class="container my-4">
    <div class="dish-details">
        <img src="{{ asset('foodimage/' . $dish->image) }}" alt="{{ $dish->title }}" class="dish-image">
        <div class="dish-title">{{ $dish->title }}</div>
    </div>

    <form action="{{ isset($orderId) ? route('order.updateOrder', $orderId) : route('order.addItem') }}" method="POST">
    @csrf
    @if(isset($orderId))
        @method('POST')
    @endif
    <input type="hidden" name="table_id" value="{{ $tableId }}">
    <input type="hidden" name="dish_id" value="{{ $dishId }}">

    <div class="addons-section">
        <label for="addons" class="form-label">Add-ons:</label>
        <div id="addons" class="form-group">
            @foreach($add_ons as $addon)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" name="addons[]"
                        @if(isset($selected_add_ons) && in_array($addon->id, $selected_add_ons)) checked @endif>
                    <label class="form-check-label" for="addon_{{ $addon->id }}">{{ $addon->addon_name }} (+{{ number_format($addon->price, 2) }} €)</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity:</label>
        <input type="number" id="quantity" name="quantity" class="form-control" min="1" required value="{{ isset($quantity) ? $quantity : 1 }}">
    </div>

    <div class="mb-3">
        <label for="comment" class="form-label">Comment:</label>
        <textarea id="comment" name="comment" class="form-control">{{ isset($comment) ? $comment : '' }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">{{ isset($orderId) ? 'Update Order' : 'Add to Order' }}</button>
</form>

</div>
</body>
</html>
