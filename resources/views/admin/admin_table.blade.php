@extends('admin')

@section('table') active @endsection

@section('content')
<section class="card">
<div class="container">
    <h1 class="mid">Tables</h1>

   
    
    <div class="row">
        @foreach($tables as $table)
        <div class="col-md-4">
            <div class="menu-card mb-4 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">Table {{ $table->id }}</h2>
                    <div class="mb-4">
                        <img src="{{ route('table.qrcode', ['id' => $table->id]) }}" alt="QR Code" class="img-fluid">
                    </div>
                    <div class="d-grid gap-2 mb-2">
                        <a href="{{ route('table.qrcode.download', ['id' => $table->id]) }}" class="btn btn-outline-danger">Download QR Code</a>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('table.show', ['id' => $table->id]) }}" class="btn btn-outline-danger">View Table</a>

    <form action="{{ route('table.destroy.latest') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the table with the highest ID?');" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">Delete Table</button>
    </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach


        <div class="col-md-4">
            <div class="menu-card mb-4 shadow-sm new-table-card">
                <form action="{{ route('table.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="new-table-button">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</section>
@endsection
