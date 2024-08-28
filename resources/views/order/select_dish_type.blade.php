<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Dish Type</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{url('CSS/table.css')}}">
</head>
<body>
<div class="header"><h1>Select Dish Type</h1></div>
<div class="container my-4">
    <form action="{{ route('order.selectDish') }}" method="GET">
        <input type="hidden" name="table_id" value="{{ $tableId }}">
        <div class="grid-container">
            @foreach($dish_types as $type)
                @php
                    $firstDish = $type->dishes->first();
                @endphp
                <button type="submit" class="dish-type-btn" name="type_id" value="{{ $type->id }}"
                    @if($firstDish) style="background-image: url('{{ asset('foodimage/' . $firstDish->image) }}');" @endif>
                    <span class="btn-text">{{ $type->type_name }}</span>
                </button>
            @endforeach
        </div>
    </form>
</div>
</body>
</html>
