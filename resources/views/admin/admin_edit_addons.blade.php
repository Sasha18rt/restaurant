@extends('admin')
@section('menu') active @endsection
@section('content')
<script src="{{ asset('index.js') }}"></script>
<div class="card out_menu" style="display: flex; justify-content: space-between;">
    <!-- Left Column for Managing Add-ons -->
    <div class="menu_form" style="flex: 1; margin-right: 20px;">
        <h1 class="mt-5" style="text-align: center;">Manage Add-ons</h1>
        <!-- Create Add-on Form -->
        <form action="{{ route('addons.store') }}" method="post" class="mb-5">
            @csrf
            <div class="mb-3">
                <label for="addon_name" class="form-label">Add-on Name:</label>
                <input type="text" name="addon_name" class="navbar-search-input" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" name="price" class="navbar-search-input" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Add-on</button>
        </form>
        
        <div class="mb-3">
            <label class="form-label">Existing Add-ons:</label>
            <ul class="list-group">
                @foreach($addOns as $addOn)
                    <li class="addon-item">
                        <span>{{ $addOn->addon_name }} ({{ $addOn->price }}$)</span>
                        <form action="{{ route('addons.destroy', $addOn->id) }}" method="post" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Right Column for Add-ons of a Specific Dish -->
    <div style="flex: 1; margin-left: 20px;">
        <h1 class="mt-5" style="text-align: center;">Add-ons for {{ $dish->title }}</h1>
        <!-- Update Add-ons Form -->
        <form action="{{ route('update_addons', $dish->id) }}" method="post" class="menu_form">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @csrf    
            <div class="mb-3">
                <label for="add_ons" class="form-label">Additional Ingredients:</label>
                @foreach($addOns as $addOn)
                    <div>
                        <input class="navbar-search-input" type="checkbox" name="add_ons[]" value="{{ $addOn->id }}" 
                               {{ $dish->addOns->contains($addOn->id) ? 'checked' : '' }}>
                        <label>{{ $addOn->addon_name }} ({{ $addOn->price }}$)</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Update Add-ons</button>
        </form>
    </div>
</div>
@endsection
