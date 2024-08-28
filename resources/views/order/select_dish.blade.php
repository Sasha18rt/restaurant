<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Dish</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .header {
    background-color: #f5f5f5;
    border-radius: 8px;
    width: 100%;
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .dish-btn {
            width: 100%;
            padding-bottom: 100%;
            position: relative;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-size: cover;
            background-position: center;
            overflow: hidden;
            color: white;
            font-size: 1.2rem;
            text-shadow: 2px 2px 4px #000000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dish-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            z-index: 1;
        }
        .btn-text {
            position: relative;
            z-index: 2; 
            width: 90%;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="header"><h1>Select Dish</h1></div>
<div class="container my-4">
    <form action="{{ route('order.addDetails') }}" method="GET">
        <input type="hidden" name="table_id" value="{{ $tableId }}">
        <input type="hidden" name="type_id" value="{{ $typeId }}">
        <div class="grid-container">
            @foreach($dishes as $dish)
                <button type="submit" class="dish-btn" name="dish_id" value="{{ $dish->id }}"
                    style="background-image: url('{{ asset('foodimage/' . $dish->image) }}');">
                    <span class="btn-text">{{ $dish->title }}</span>
                </button>
            @endforeach
        </div>
    </form>
</div>
</body>
</html>
