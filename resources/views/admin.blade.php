<!DOCTYPE html>
<html>
<head>
	<title>Admin</title>

	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	

	

	<link rel="stylesheet" type="text/css" href="{{url('CSS/admin.css')}}">

	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e3a49d370f.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
	
    

	
</head>
<body class="overlay-scrollbar bg ">
	
	<div class="navbar">
		
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link">
					<i class="fas fa-bars" onclick="collapseSidebar()"></i>
				</a>
			</li>
			<li class="nav-item">
				<div>
					<h5>NAME</h5>
				</div>
			</li>
		</ul>
		
		
		<ul class="navbar-nav nav-right">
			<li class="nav-item mode">
				<a class="nav-link" href="#" onclick="switchTheme()">
					<i class="fas fa-moon dark-icon"></i>
					<i class="fas fa-sun light-icon"></i>
				</a>
			</li>
			<li class="nav-item avt-wrapper">
				<div class="avt dropdown">
					<img src="{{ asset('assets/taut.png') }}" alt="User image" class="dropdown-toggle" data-toggle="user-menu">

					<ul id="user-menu" class="dropdown-menu">
						<li class="dropdown-menu-item">
							<a class="dropdown-menu-link" href="{{ route('profile.show') }}">
								<div>
									<i class="fas fa-user-tie"></i>
								</div>
								<span>{{ Auth::user()->name }}</span> 
							</a>
						</li>
						<li class="dropdown-menu-item">
							<a href="#" class="dropdown-menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
								<div>
									<i class="fas fa-sign-out-alt"></i>
								</div>
								<span>
									Logout
									<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
										@csrf
									</form>
								</span>
							</a>
						</li>
					</ul>
				</div>
			</li>
		</ul>

	</div>
	<div class="sidebar">
		<ul class="sidebar-nav">
			<li class="sidebar-nav-item">
				<a href="/users" class="sidebar-nav-link  @yield('users')">
					<div>
						<i class="fa-solid fa-users"></i>
					</div>
					<span>Users</span>
				</a>
			</li>
			<li class="sidebar-nav-item">
				<a href="/menu" class="sidebar-nav-link @yield('menu') ">
					<div>
						<i class="fa-solid fa-utensils"></i>
					</div>
					<span>Menu</span>
				</a>
			</li>
			<li  class="sidebar-nav-item">
				<a href="/tables" class="sidebar-nav-link @yield('table')">
					<div>
                    <i class="fa-solid fa-table-cells-large"></i>
					</div>
					<span>Tables</span>
				</a>
			</li>
			<li class="sidebar-nav-item">
    <a href="/orders" class="sidebar-nav-link @yield('orders')">
        <div>
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
        <span>Orders</span>
    </a>
</li>
			<li  class="sidebar-nav-item">
				<a href="/reviews" class="sidebar-nav-link @yield('reviews')">
					<div>
						<i class="fa-solid fa-comments"></i>
					</div>
					<span>Reviews</span>
				</a>
			</li>
			<li  class="sidebar-nav-item">
				<a href="/areservation" class="sidebar-nav-link @yield('reservation')">
					<div>
                    <i class="fa-solid fa-user-pen"></i>
					</div>
					<span>Reservation</span>
				</a>
			</li>
		</ul>
	</div>
    <div class="wrapper">
	@yield('content')

  
    </div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
	<script src="index.js"></script>
</body>
</html>
