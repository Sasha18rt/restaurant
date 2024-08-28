@extends('admin')
@section('users') active @endsection
@section('content')
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css">
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>

</head>
<div class="card">
    <table id="example" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Functions</th>
            <th>Occupation</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td> 
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at }}</td> 
            <td>
                @if($user->usertype != '1')
                <a href="{{ url('/deleteuser', $user->id) }}" class="btn btn-delete-user">Delete</a>
                @else
                <span class="text-muted">Not Allowed</span>   
                @endif
                @if($user->usertype == '0')
                <a href="{{ url('/waiter', $user->id) }}" class="btn btn-hire">Hire</a>
                @elseif($user->usertype == '2')
                <a href="{{ url('/waiter', $user->id) }}" class="btn btn-delete-user">fire</a>
                @endif
            </td>
            <td>
                @if($user->usertype == '0')
                Regular Customer
                @elseif($user->usertype == '1')
                Admin
                @elseif($user->usertype == '2')
                Waiter
                @endif
            </td>
        
        </tr>
        @endforeach
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#example').DataTable({
        layout: {
            bottomEnd: {
                paging: {
                    boundaryNumbers: false
                }
            }
        }
    });
});
</script>
</div>
@endsection






