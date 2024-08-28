<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/e3a49d370f.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="{{url('CSS/app.css')}}">
</head>
<body class="bg-white text-dark">
<header class="header-area header-sticky">
    <div class="container-fluid">  
      <div class="row align-items-center">
        <div class="col-md-3 ">
          <h5 class="my-0 mr-md-auto font-weight-normal ">NAME</h5>
        </div>
        <div class="col-md-9 d-flex justify-content-end">
          <nav class="navbar navbar-expand-md">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
              @if (Route::has('login'))
                  @auth
                    @if(Auth::user()->usertype == 2) 
                    <li class="nav-item">
                      <a class="nav-link btn " href="{{ route('order.create') }}">Зробити замовлення</a>
                    </li>
                    @endif
                <li class="nav-item">
                  <a class="nav-link @yield('main') " href="/">Main</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link @yield('about') " href="#about " >About us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link @yield('Menu')" href="#menu">Menu</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link @yield('Galery')" href="#reservation">Contact us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link @yield('review')" href="/review">Reviews</a>
                </li>
              
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                          </form>
                        </li>
                      </ul>
                    </li>
                  @else
                    <li class="nav-item">
                      <a class="nav-link" href="/login">Log in</a>
                    </li>
                  @endauth
                @endif
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <div>
    @yield('content')
  </div>

  <footer class="bg-dark text-white">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="col-md-4 text-center">
        <div class="logo">
          <h5 class="my-0 font-weight-normal">NAME</h5>
        </div>
      </div>
      <div class="col-md-4 d-flex align-items-center justify-content-end">
        <span class="me-3"><i class="fas fa-phone"></i> <a href="tel:your-phone-number">+37066262000</a></span>
        <span><i class="fas fa-map-marker-alt"></i> Your Address</span>
      </div>
    </div>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
