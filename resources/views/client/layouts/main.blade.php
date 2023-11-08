@php use App\Models\User;use Illuminate\Support\Facades\Storage; @endphp
    <!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap"
          rel="stylesheet">

    <title>Lumos</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('adminlte/dist/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="{{asset('assets/css/fontawesome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/templatemo-sixteen.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/reset.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/qty.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/rating.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

</head>

<body>

<!-- ***** Preloader Start ***** -->
<div id="preloader">
    <div class="jumper">
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>
<!-- ***** Preloader End ***** -->

<!-- Header -->
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('client.products.index') }}">
                <table>
                    <th><img src="{{Storage::url('view/wand.svg')}}" alt="" width="30"></th>
                    <th>
                        <h2>Lumos <em>Hand Made</em></h2>
                    </th>
                </table>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                    aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.products.index') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <div class="dropdown">
                            <a class="nav-link" data-toggle="dropdown">Продукты</a>
                            <div class="dropdown-menu">
                                @foreach ($categories as $category)
                                    <a class="dropdown-item"
                                       href="{{ route('client.products.filter', $category['title']) }}">{{ $category['title_rus'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.cart') }}">Корзина</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('client.about') }}" style="white-space: pre">О нас</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.orders.index') }}">Заказы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.products.liked') }}">Отложенное</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.show', session('user.id')) }}"
                               style="white-space: pre">{{ session('user.name') }}</a>
                        </li>
                        @can('role', [User::class, User::ROLE_SALER])
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.index') }}"
                                   style="white-space: pre">Админка</a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                                Выход</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>

@yield('content')

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="inner-content">
                    <ul class="social-icons">
                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                    <p>Copyright &copy; 2022-{{ now()->year }} <a rel="nofollow noopener"
                                                                  href="{{ route('client.products.index') }}"
                                                                  target="_blank">Lumos</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>


<!-- Bootstrap core JavaScript -->

<script language="text/Javascript">
    cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
    function clearField(t) { //declaring the array outside of the
        if (!cleared[t.id]) { // function makes it static and global
            cleared[t.id] = 1; // you could use true and false, but that's more typing
            t.value = ''; // with more chance of typos
            t.style.color = '#fff';
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<!-- Additional Scripts -->
<script src="{{asset('assets/js/custom.js')}}"></script>
<script src="{{asset('assets/js/owl.js')}}"></script>
<script src="{{asset('assets/js/slick.js')}}"></script>
<script src="{{asset('assets/js/isotope.js')}}"></script>
<script src="{{asset('assets/js/accordions.js')}}"></script>
<script src="{{asset('assets/js/jquery.mixitup.min.js')}}"></script>
<script src="{{asset('assets/js/main.js')}}"></script>
<script src="{{asset('assets/js/modernizr.js')}}"></script>
<script src="{{asset('assets/js/qty.js')}}"></script>
<script src="{{asset('assets/js/summernote.js')}}"></script>
</body>

</html>
