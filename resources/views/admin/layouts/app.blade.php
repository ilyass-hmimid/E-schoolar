<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Centre d'Orientation</title>
<link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Custom CSS -->
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
</li>
{{-- <li class="nav-item d-none d-sm-inline-block">
<a href="index3.html" class="nav-link">Home</a>
</li> --}}
{{-- <li class="nav-item d-none d-sm-inline-block">
<a href="#" class="nav-link">Contact</a>
</li> --}}
</ul>

<ul class="navbar-nav ml-auto">

{{-- <li class="nav-item">
<a class="nav-link" data-widget="navbar-search" href="#" role="button">
<i class="fas fa-search"></i>
</a>
<div class="navbar-search-block">
<form class="form-inline">
<div class="input-group input-group-sm">
<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
<div class="input-group-append">
<button class="btn btn-navbar" type="submit">
<i class="fas fa-search"></i>
</button>
<button class="btn btn-navbar" type="button" data-widget="navbar-search">
<i class="fas fa-times"></i>
</button>
</div>
</div>
</form>
</div>
</li> --}}

{{-- <li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="#">
<i class="far fa-comments"></i>
<span class="badge badge-danger navbar-badge">3</span>
</a>
<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
<a href="#" class="dropdown-item">

<div class="media">
<img src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
<div class="media-body">
<h3 class="dropdown-item-title">
Brad Diesel
<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
</h3>
<p class="text-sm">Call me whenever you can...</p>
<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
</div>
</div>

</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item">

<div class="media">
<img src="https://adminlte.io/themes/v3/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
<div class="media-body">
<h3 class="dropdown-item-title">
John Pierce
<span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
</h3>
<p class="text-sm">I got your message bro</p>
<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
</div>
</div>

</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item">

<div class="media">
<img src="https://adminlte.io/themes/v3/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
<div class="media-body">
<h3 class="dropdown-item-title">
Nora Silvester
<span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
</h3>
<p class="text-sm">The subject goes here</p>
<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
</div>
</div>

</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
</div>
</li> --}}
{{--
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="#">
<i class="far fa-bell"></i>
<span class="badge badge-warning navbar-badge">15</span>
</a>
<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
<span class="dropdown-header">15 Notifications</span>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item">
<i class="fas fa-envelope mr-2"></i> 4 new messages
<span class="float-right text-muted text-sm">3 mins</span>
</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item">
<i class="fas fa-users mr-2"></i> 8 friend requests
<span class="float-right text-muted text-sm">12 hours</span>
</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item">
<i class="fas fa-file mr-2"></i> 3 new reports
<span class="float-right text-muted text-sm">2 days</span>
</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
</div>
</li> --}}
<li class="nav-item">
<a class="nav-link" data-widget="fullscreen" href="#" role="button">
<i class="fas fa-expand-arrows-alt"></i>
</a>
{{-- </li>
<li class="nav-item">
<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
<i class="fas fa-th-large"></i>
</a>
</li> --}}
</ul>
</nav>


<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <router-link to="/home" class="brand-link">
    <img src="{{ asset('imgs/logo.png') }}" style="width: 50px !important;" alt="">

{{-- <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
<span class="brand-text font-weight-light"style="font-weight: 900!important; font-size : 14px !important; margin-left : 10px !important;">CENTRE CONCEPT ETUDES</span>
</router-link>

<div class="sidebar">
{{--
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
<div class="image">
    <img src="{{ asset('imgs/HassanAabidi.jpeg') }}" style="border-radius: 50%;" alt="">

{{-- <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"> --}}
{{-- </div>
<div class="info">
<a href="#" class="d-block">Hassan Aabidi</a>
</div>
</div> --}}



<nav class="mt-2">
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


{{-- <li class="nav-item">
<router-link to="/dashboard" active-class="active" class="nav-link">
<i class="nav-icon fas fa-tachometer-alt"></i>
<p>
Dashboard
</p>
</a>
</li> --}}
<li class="nav-item">
    <router-link to="/home" active-class="active" class="nav-link">
    <i class="nav-icon fas fa-house-user"></i>
    <p>
        Accueil
    </p>
    </a>
    </li>

{{-- <li class="nav-item">
    <router-link to="/appointments" active-class="active" class="nav-link">
    <i class="nav-icon fas fa-calendar-alt"></i>
    <p>
    Appointments
    </p>
    </router-link>
    </li> --}}


    @if(Auth::check() && Auth::user()->role == 'admin')
    <li class="nav-item">
        <router-link to="users" active-class="active" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>
        Utilisateurs
        </p>
        </router-link>
        </li>
        @endif

        @if(Auth::check() && Auth::user()->role == 'admin')
        <li class="nav-item">
            <router-link to="/professeurs" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-user-tie"></i>
            <p>
                Professeurs
            </p>
            </router-link>
            </li>
            @endif


            @if(Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'admin_assistant'))
            <li class="nav-item">
                <router-link to="/students"
                             :class="{ active: $route.path.startsWith('/students') }"
                             class="nav-link">
                <i class="nav-icon fas fa-user-graduate"></i>
                <p>
                   Etudiants
                </p>
                </router-link>
                </li>
                @endif

                @if(Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'admin_assistant'))
                <li class="nav-item">
                    <router-link to="/liste_absence" active-class="active" class="nav-link">
                    <i class="nav-icon fas fa-users-slash"></i>
                    <p>
                       Absences
                    </p>
                    </router-link>
                    </li>
                    @endif



                @if(Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'admin_assistant'))
                <li class="nav-item">
                    <router-link to="/paiments" active-class="active" class="nav-link">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>
                        Paiements
                    </p>
                    </router-link>
                    </li>
                    @endif

                    @if(Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'admin_assistant'))
                    <li class="nav-item">
                        <router-link to="/valeurs_paiments" active-class="active" class="nav-link">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>
                            Valeurs des paiements
                        </p>
                        </router-link>
                        </li>
                        @endif


                        @if(Auth::check() && Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <router-link to="/salaires" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-money-bill" style="fill : white !important;"></i>
                            <p>
                                Salaires
                            </p>
                            </router-link>
                            </li>
                            @endif


                            @if(Auth::check() && Auth::user()->role == 'admin')
                            <li class="nav-item">
                                <router-link to="/valeurs_salaires" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-euro-sign"></i>
                                <p>
                                    Valeurs des salaires
                                </p>
                                </router-link>
                                </li>
                                @endif


                                @if(Auth::check() && Auth::user()->role == 'admin')
                                <li class="nav-item">
                                    <router-link to="/enseignements" active-class="active" class="nav-link">
                                    <i class="nav-icon fas fa-chalkboard"></i>
                                    <p>
                                        Enseignements
                                    </p>
                                    </router-link>
                                    </li>
                                    @endif


                                    @if(Auth::check() && Auth::user()->role == 'professeur')
                                    <li class="nav-item">
                                        <router-link to="/EspaceProfesseur" active-class="active" class="nav-link">
                                        <i class="nav-icon fas fa-money-bill"></i>
                                        <p>
                                            Mon salaire
                                        </p>
                                        </router-link>
                                        </li>
                                        @endif

                                        @if(Auth::check() && Auth::user()->role == 'professeur')
                                        <li class="nav-item">
                                            <router-link to="/classes_professeur" active-class="active" class="nav-link">
                                            <i class="nav-icon fas fa-users"></i>
                                            <p>
                                                Mes étudiants
                                            </p>
                                            </router-link>
                                            </li>
                                            @endif



                                        @if(Auth::check() && Auth::user()->role != 'professeur')
                                    <li class="nav-item">
                                        <router-link to="/niveaux" active-class="active" class="nav-link">
                                        <i class="nav-icon fas fa-layer-group"></i>
                                        <p>
                                            Niveaux
                                        </p>
                                        </router-link>
                                        </li>
                                        @endif

                                        @if(Auth::check() && Auth::user()->role != 'professeur')
                                        <li class="nav-item">
                                            <router-link to="/filieres" active-class="active" class="nav-link">
                                            <i class="nav-icon fas fa-book"></i>
                                            <p>
                                                Filières
                                            </p>
                                            </router-link>
                                            </li>
                                            @endif

                                            @if(Auth::check() && Auth::user()->role != 'professeur')
                                            <li class="nav-item">
                                                <router-link to="/matieres" active-class="active" class="nav-link">
                                                    <i class="nav-icon fas fa-file-signature"></i>
                                                    <p>Matières</p>
                                                </router-link>
                                            </li>
                                            @endif











        {{-- <li class="nav-item">
            <router-link to="/setings" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>
            Parametres
            </p>
            </router-link>
            </li> --}}

            {{-- <li class="nav-item">
                <router-link to="profile" active-class="active" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>
                Profile
                </p>
                </router-link>
                </li> --}}
{{--
                <li class="nav-item">
                    <form method="POST" action="{{ route('profile') }}">
                        @csrf
                        <a href="{{ route('profile') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link nav-link-hover">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Profile
                            </p>
                        </a>
                    </form>
                </li> --}}



                {{-- <li class="nav-item">
                    <router-link to="logout" href="route('logout')" active-class="active" class="nav-link">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p>
                    Déconnexion
                    </p>
                    </router-link>
                    </li> --}}

                    {{-- @auth --}}
                    <li class="nav-item">
                        <a href="#" onclick="confirmLogout(event)" class="nav-link nav-link-hover">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Déconnexion
                            </p>
                        </a>
                    </li>

                    <script>
                        function confirmLogout(event) {
                            event.preventDefault();

                            if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
                                // Si l'utilisateur confirme, soumettez le formulaire de déconnexion
                                document.querySelector('#logout-form').submit();
                            }
                        }
                    </script>

                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>

                    {{-- @endauth --}}



</ul>
</nav>

</div>

</aside>

<div class="content-wrapper">
 <router-view></router-view>
{{-- <div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1 class="m-0">Starter Page</h1>
</div>
<div class="col-sm-6">
<ol class="breadcrumb float-sm-right">
<li class="breadcrumb-item"><a href="#">Home</a></li>
<li class="breadcrumb-item active">Starter Page</li>
</ol>
</div>
</div>
</div>
</div>


<div class="content">
<div class="container-fluid">
<div class="row">
<div class="col-lg-6">
<div class="card">
<div class="card-body">
<h5 class="card-title">Card title</h5>
<p class="card-text">
Some hdhdhdjquick example text to build on the card title and make up the bulk of the card's
content.
</p>
<a href="#" class="card-link">Card link</a>
<a href="#" class="card-link">Another link</a>
</div>
</div>
<div class="card card-primary card-outline">
<div class="card-body">
<h5 class="card-title">Card title</h5>
<p class="card-text">
Some quick example text to build on the card title and make up the bulk of the card's
content.
</p>
<a href="#" class="card-link">Card link</a>
<a href="#" class="card-link">Another link</a>
</div>
</div>
</div>

<div class="col-lg-6">
<div class="card">
<div class="card-header">
<h5 class="m-0">Featured</h5>
</div>
<div class="card-body">
<h6 class="card-title">Special title treatment</h6>
<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
<a href="#" class="btn btn-primary">Go somewhere</a>
</div>
</div>
<div class="card card-primary card-outline">
<div class="card-header">
<h5 class="m-0">Featured</h5>
</div>
<div class="card-body">
<h6 class="card-title">Special title treatment</h6>
<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
<a href="#" class="btn btn-primary">Go somewhere</a>
</div>
</div>
</div>

</div>

</div>
</div>
--}}
</div>


<aside class="control-sidebar control-sidebar-dark">

<div class="p-3">
<h5>Title</h5>
<p>Sidebar content</p>
</div>
</aside>


{{-- <footer class="main-footer">

<div class="float-right d-none d-sm-inline">
Anything you want
</div>

<strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
</footer> --}}
</div>

</body>
</html>
