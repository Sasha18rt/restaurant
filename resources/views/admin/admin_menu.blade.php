@extends('admin')
@section('menu') active @endsection
@section('content')
<div class="card out_menu "> 
    <div>
        <form action="{{ route('submit_dish') }}" method="post" enctype="multipart/form-data" class="menu_form">
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
            <div class="dropdown-menu-item select">
                <label for="dish_type" class="form-label">Dish type:</label>
                <div class="d-flex align-items-center">
                    <select name="dish_type_id" class="form-select" id="dish_type_select">
                        @foreach($dishTypes as $type)
                            <option class="dropdown-menu-link" value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="gear_icon_btn" class="card" style="margin-left: 3px; font-size: 18px; box-shadow: none; cursor: pointer;  border: none; margin-top: 8px;">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="dish_name" class="form-label">Dish Name:</label>
                <input type="text" id="dish_name" name="dish_name" class="navbar-search-input" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" id="price" name="price" class="navbar-search-input" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="navbar-search-input" required></textarea>
            </div>

            <div class="mb-3">
                <label for="photo" class="form-label">Dish Photo:</label>
                <input type="file" id="photo" name="photo" class="navbar-search-input  photo" accept="image/*" required>
            </div>

            <button type="submit " class="btn btn-primary ">Add Dish</button>
        </form>
       
        <div id="type_management_form" style="margin-top: 20px;">
            <div class="mid">
                <h1>Type settings</h1>
            </div>
            <form id="add_type_form" class="menu_form" action="{{ route('add_new_dish_type') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="new_dish_type_input" class="form-label">New Dish Type:</label>
                <input type="text" id="new_dish_type_input" name="new_dish_type" class="navbar-search-input" placeholder="New dish type">
            </div>
            <button type="submit" id="add_new_type_btn" class="btn btn-primary">Add New Type</button>
        </form>
            <form id="manage_type_form" class="menu_form" action="{{ route('manage_dish_type') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="manage_dish_type_select" class="form-label">Select Dish Type to Manage:</label>
                    <select name="dish_type_id" class="form-select" id="manage_dish_type_select">
                        @foreach($dishTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_dish_type_name" class="form-label">New Dish Type Name:</label>
                    <input type="text" id="new_dish_type_name" name="new_dish_type_name" class="navbar-search-input">
                </div>
                <div class="d-flex">
                    <button type="submit" name="action" value="update" class="btn btn-primary" style="margin-right: 10px;">Update Type</button>
                    <button type="submit" name="action" value="delete" class="btn  btn-delete">Delete Type</button>
                </div>
            </form>

            <div class="mid">
                <h1>Reorder Types</h1>
            </div>
            <ul id="dish_type_list" class="list-group ">
    @foreach($dishTypes as $type)
        <li class="list-group-item card card-title" data-id="{{ $type->id }}">
            {{ $type->type_name }}
            <span class="drag-handle">⋮⋮</span>
        </li>
    @endforeach
</ul>

            <button id="save_order_btn" class="btn  btn-primary" style="margin: 20px 30px 10px 40%; ">Save Order</button>
        </div>
    </div>
    
    <section class="section" id="offers">
        <div class="container">
            <h1 class="mid">Dishes</h1>
            <div class="row">
                <form id="filterForm">
                    <label for="filter_dish_type_result" class="form-label">Filter by Dish type:</label>
                    <select id="filter_dish_type_result" name="filter_dish_type" class="form-select">
                        <option value="All">All</option>
                        @foreach($dishTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row" id="tabs">
                        <div class="col-lg-12">
                            <div class="heading-tabs">
                                <div class="row">
                                    <div class="col-lg-6 offset-lg-3"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <section class='tabs-content '>
                                <div class="row ">
                                    @foreach($menu as $key => $menuItem)
                                    <div class="col-lg-6 mb-4 ">
                                        <article id='tabs-{{ $key + 1 }}' data-type="{{ $menuItem->type_id }}">
                                            <div class="menu-card">
                                                <div class="row no-gutters">
                                                    <div class="col-md-4">
                                                        <img src="/foodimage/{{ $menuItem->image }}" class="card-img" alt="{{ $menuItem->title }}">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $menuItem->title }}</h5>
                                                            <p class="card-text">{{ $menuItem->description }}</p>
                                                            <div class="price">
                                                                <p>${{ $menuItem->price }}</p>
                                                            </div>
                                                            <div class="d-flex">
                                                            <form action="{{ route('delete_dish', $menuItem->id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this dish?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-delete">Delete</button>
</form>

                                                                <a href="{{ route('edit_menu_item', $menuItem->id) }}" class=" btn-update">Update</a>
                                                                <a href="{{ route('edit_addons', $menuItem->id) }}" class="btn-add">Add-ons</a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('type_management_form').style.display = 'none';
    
    document.getElementById('filter_dish_type_result').addEventListener('change', filterMenu);
    document.getElementById('gear_icon_btn').addEventListener('click', toggleTypeManagementForm);

    function filterMenu() {
        var selectedType = document.getElementById('filter_dish_type_result').value;
        var articles = document.querySelectorAll('.tabs-content article');

        articles.forEach(function(article) {
            var dishType = article.getAttribute('data-type');

            if (selectedType === 'All' || dishType === selectedType) {
                article.style.display = 'block';
            } else {
                article.style.display = 'none';
            }
        });
    }

    function toggleTypeManagementForm() {
        var typeManagementForm = document.getElementById('type_management_form');
        typeManagementForm.style.display = (typeManagementForm.style.display === 'none' || typeManagementForm.style.display === '') ? 'block' : 'none';
    }

    // Initialize Sortable
    var sortable = new Sortable(document.getElementById('dish_type_list'), {
        animation: 150,
        ghostClass: 'blue-background-class'
    });

    // Save order
    document.getElementById('save_order_btn').addEventListener('click', function() {
        var order = sortable.toArray();
        fetch('{{ route('reorder_dish_types') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order updated successfully!');
            } else {
                alert('Failed to update order.');
            }
        });
    });
});

</script>
@endsection
