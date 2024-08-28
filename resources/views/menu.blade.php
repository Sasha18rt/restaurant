<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }
        .header {
    background-color: #164705;
    color: white;
    text-align: center;
    padding: 20px 0;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1000;
}
        .header .back-icon {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .heading-tabs {
    overflow-x: auto;
    white-space: nowrap;
    background-color: #fff;
    padding: 15px 0;
    margin: 0;
    border-bottom: 2px solid #ddd;
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.heading-tabs a {
    padding: 10px 20px;
    display: inline-block;
    color: #164705;
    font-weight: bold;
    text-decoration: none;
    transition: color 0.3s, background-color 0.3s;
    border-radius: 30px;
    margin: 0 5px;
}

.heading-tabs a.active {
    background-color: #164705;
    color: #fff;
}

        .heading-tabs ul {
            display: flex;
            padding: 0;
            margin: 0;
        }

        .tab-item {
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
            margin-bottom: 0px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
            width: 97%; 
        }
        .menu-image {
            width: 100%;
            height: auto;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .card-body {
            padding: 10px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2a2a2a;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 1rem;
            color: #454545;
            margin-bottom: 15px;
        }
        .price h6 {
            font-size: 1.25rem;
            font-weight: bold;
            color: #164705;
            margin-bottom: 0;
            text-align: right;
        }
        .menu-divider {
            font-size: 1.5rem;
            color: #164705;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
            position: relative;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .row {

    margin-right: 0px;
    margin-left: 0px;
}
    </style>
</head>
<body>
<div class="header">
    <div class="back-icon" onclick="window.history.back();">&#8592;</div>
    <h2>Table {{ $table->id }}</h2>
</div>

<section class="section" id="menu">
    <div class="heading-tabs text-center">
        <ul class="list-inline d-flex flex-nowrap">
            <li class="list-inline-item">
                <a onclick="showTab('All', this)" href="#menu" class="active">All</a>
            </li>
            @foreach($dishTypes as $type)
                <li class="list-inline-item">
                    <a onclick="showTab('{{ $type->id }}', this)" href="#menu">{{ $type->type_name }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="row" id="tabs">
        <div class="container-fluid px-0">
            <section class="tabs-content" id="tab-All">
                @foreach($dishTypes as $type)
                    <div class="menu-divider">{{ $type->type_name }}</div>
                    <div class="row justify-content-center no-gutters">
                        @foreach($menu->where('type_id', $type->id) as $dish)
                        <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex justify-content-center">
    <div class="tab-item card" data-id="{{ $dish->id }}">
        <img src="{{ asset('foodimage/' . $dish->image) }}" alt="{{ $dish->title }}" class="menu-image card-img-top">
        <div class="card-body">
            <h4 class="card-title">{{ $dish->title }}</h4>
            <p class="card-text">{{ $dish->description }}</p>
            <div class="price">
                <h6>{{ $dish->price }}€</h6>
            </div>
        </div>
    </div>
</div>

                        @endforeach
                    </div>
                @endforeach
            </section>

            @foreach($dishTypes as $type)
                <section class="tabs-content" id="tab-{{ $type->id }}" style="display:none;">
                    <div class="menu-divider">{{ $type->type_name }}</div>
                    <div class="row justify-content-center no-gutters">
                        @foreach($menu->where('type_id', $type->id) as $dish)
                            <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex justify-content-center">
                                <div class="tab-item card">
                                    <img src="{{ asset('foodimage/' . $dish->image) }}" alt="{{ $dish->title }}" class="menu-image card-img-top">
                                    <div class="card-body">
                                        <h4 class="card-title">{{ $dish->title }}</h4>
                                        <p class="card-text">{{ $dish->description }}</p>
                                        <div class="price">
                                            <h6>{{ $dish->price }}€</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</section>

<script>
    function showTab(tabName, linkElement) {
        var tabs = document.querySelectorAll('.tabs-content');
        tabs.forEach(function(tab) {
            tab.style.display = 'none';
        });

        if (tabName === 'All') {
            document.getElementById('tab-All').style.display = 'block';
        } else {
            document.getElementById('tab-' + tabName).style.display = 'block';
        }

        var links = document.querySelectorAll('.heading-tabs a');
        links.forEach(function(link) {
            link.classList.remove('active');
        });
        linkElement.classList.add('active');
        event.preventDefault();
    }

    window.onload = function() {
        showTab('All', document.querySelector('.heading-tabs a'));
    };

    document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-item');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            const dishId = this.getAttribute('data-id'); 
            const tableId = {{ $table->id }}; 

            window.location.href = "{{ url('/table/order') }}?table_id=" + tableId + "&dish_id=" + dishId;
        });
    });
});

</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>
