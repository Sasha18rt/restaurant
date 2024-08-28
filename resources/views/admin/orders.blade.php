@extends('admin')

@section('orders') active @endsection

@section('content')
<section class="card">
<div class="container">
    <h1 class="mid">Orders</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row" id="orders-container">
        @foreach($orders as $order)
        <div class="col-md-6 d-flex align-items-stretch order-card"> 
            <div class="menu-card mb-4 shadow-sm w-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="card-title mb-2">Order #{{ $order->id }}</h2> 
                    <p class="card-text">Table: {{ $order->table_id ? $order->table_id : 'Takeaway' }}</p>
                    <p class="card-text">{{ $order->quantity }} {{ $order->dish->title }}</p>
                    <p class="card-text" style="padding-left: 50px; ">
                        @if($order->addons->count() > 0)
                            @foreach($order->addons as $addon)
                               <p class="card-text">    {{ $addon->addon_name }}{{ !$loop->last ? ', ' : '' }} </p>
                            @endforeach
                        @else
                            &nbsp;
                        @endif
                    </p>
                    <p class="card-text">
                        @if($order->comment)
                            Comment: {{ $order->comment }}
                        @else
                            &nbsp;
                        @endif
                    </p>

                    <form action="{{ route('orders.complete', ['id' => $order->id]) }}" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Mark as Completed</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
   function fetchLatestOrders() {
    $.ajax({
        url: '{{ route("orders.latest") }}',
        type: 'GET',
        success: function(data) {
            let ordersContainer = $('#orders-container');
            ordersContainer.empty();

            data.forEach(function(order) {
                let addonsList = '';
                if (order.addons.length > 0) {
                    addonsList = order.addons.map(addon => `<p class="card-text">${addon.addon_name}${order.addons.indexOf(addon) !== order.addons.length - 1 ? ', ' : ''}</p>`).join('');
                } else {
                    addonsList = '&nbsp;';
                }

                let comment = order.comment ? `Comment: ${order.comment}` : '&nbsp;';

                let orderHtml = `
                    <div class="col-md-6 d-flex align-items-stretch order-card">
                        <div class="menu-card mb-4 shadow-sm w-100">
                            <div class="card-body d-flex flex-column">
                                <h2 class="card-title mb-2">Order #${order.id}</h2>
                                <p class="card-text">Table: ${order.table_id ? order.table_id : 'Takeaway'}</p>
                                <p class="card-text">${order.quantity} ${order.dish.title}</p>
                                <p class="card-text" style="padding-left: 50px;">${addonsList}</p>
                                <p class="card-text">${comment}</p>
                                <form action="/orders/${order.id}/complete" method="POST" class="mt-auto">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">Mark as Completed</button>
                                </form>
                            </div>
                        </div>
                    </div>
                `;

                ordersContainer.append(orderHtml);
            });
        }
    });
}

setInterval(fetchLatestOrders, 5000); 

</script>
@endsection
