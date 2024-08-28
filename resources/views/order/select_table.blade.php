<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Table or Takeout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{url('CSS/table.css')}}">
</head>
<body>
<div class="header">
    <h1 class="text-dark">Select Table or Takeout</h1>
</div>
<div class="container my-4">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group mt-3">
        <label for="custom_table_id" class="form-label">Table Number:</label>
        <input type="number" id="custom_table_id" name="custom_table_id" class="form-control">
    </div>

    <div class="btn-group btn-group-toggle d-flex justify-content-center" data-toggle="buttons">
        <label class="btn btn-outline-primary flex-fill">
            <input type="radio" name="table_option" id="takeout" value="takeout" autocomplete="off"> Takeout
        </label>
        @foreach($tables as $table)
            <label class="btn btn-outline-primary flex-fill">
                <input type="radio" name="table_option" id="table_{{ $table->id }}" value="{{ $table->id }}" autocomplete="off"> Table {{ $table->id }}
            </label>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('input[name="table_option"]').on('change', function() {
            var selectedValue = $(this).val();
            window.location.href = "{{ route('order.create') }}" + "?table_id=" + selectedValue;
        });
        $('#custom_table_id').on('change', function() {
            var customTableId = $(this).val();
            if (customTableId) {
                window.location.href = "{{ route('order.create') }}" + "?table_id=" + customTableId;
            }
        });
    });
</script>
</body>
</html>
