<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="favicon.ico" type="image/ico"/>

    <title>{{ $title ?? '' }}</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/custom.min.css" rel="stylesheet">

</head>

<body class="nav-sm">
<div class="container body">
    <div class="main_container">
        @section('left_menu')
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="{{ route('main') }}" class="site_title"><i class="fa fa-home" aria-hidden="true"></i> <span>МС портал</span></a>
                    </div>

                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            @if(Auth::user()->image)
                                <img src="{{ Auth::user()->image }}" alt="..." class="img-circle profile_img">
                            @else
                                <img src="/images/male.png" alt="..." class="img-circle profile_img">
                            @endif
                        </div>
                        <div class="profile_info">
                            <span>Здравствуйте,</span>
                            <h2>{{ Auth::user()->name }}</h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br/>

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a href="{{ route('main') }}"><i class="fa fa-tachometer" aria-hidden="true"></i>Рабочий
                                        стол </a></li>
                                <li><a href="{{ route('singleForm') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i>Отдельные анкеты </a></li>
                                <li><a href="{{ route('groupForm') }}"><i class="fa fa-book" aria-hidden="true"></i>Группа анкет </a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                </div>
            </div>
        @show

        @section('top_nav')
        <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                                   aria-expanded="false">
                                    @if(Auth::user()->image)
                                        <img src="{{ Auth::user()->image }}" alt="...">
                                    @else
                                        <img src="/images/male.png" alt="...">
                                    @endif
                                    {{ Auth::user()->login }}
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li><a href="{{ route('logout') }}"><i class="fa fa-sign-out pull-right"></i> Log
                                            Out</a></li>
                                </ul>
                            </li>


                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top navigation -->
        @show
        <div class="right_col" role="main">

        @yield('content')

        @section('footer')
            <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        <?php echo 'Разработано для выставки домов "Малоэтажная Страна". 2014 - ' . date("Y") ?>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="/js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/js/bootstrap.min.js"></script>
<!-- Custom Theme Scripts -->
    <script src="/js/custom.min.js"></script>

@show

@section('user_script')

@show

</body>
</html>
