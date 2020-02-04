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
    <link rel="icon" href="images/favicon.ico" type="image/ico"/>

    <title>{{ $title ?? '' }}</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <!-- dataTables -->
    <link href="/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/custom.min.css" rel="stylesheet">
    <link href="/css/select2.min.css" rel="stylesheet">
</head>

<body class="nav-md">
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
                                <li><a><i class="fa fa-file-text-o" aria-hidden="true"></i> Документы <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Расчет по арендатору</a></li>
                                        <li><a href="#">Расчет за период</a></li>
                                        <li><a href="{{ route('inet-conn') }}">Подключения к интернет</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-address-book-o" aria-hidden="true"></i> Контакты <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('renters') }}">Арендаторы</a></li>
                                        @if(\App\User::hasRole('admin'))
                                        <li><a href="{{ route('divisions') }}">Наши юрлица</a></li>
                                        @endif
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-users" aria-hidden="true"></i> Маркетинг <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Анкеты</a></li>
                                        <li><a href="#">Источники медиарекламы</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-bar-chart-o"></i> Отчеты <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Анкетирование</a></li>
                                        <li><a href="{{ route('visit-report') }}">Посещаемость выставки</a></li>
                                        <li><a href="{{ route('work-report') }}">Присутствие на выставке</a></li>
                                        @if(\App\User::hasRole('admin') || \App\User::hasRole('director'))
                                        <li><a href="{{ route('it-cost') }}">Затраты ИТ</a></li>
                                        @endif
                                        <li><a href="#">Потребление эл. энергии <span class="fa fa-chevron-down"></span></a>
                                            <ul class="nav child_menu">
                                                <li><a href="#">Счетчики общие</a></li>
                                                <li><a href="#">Счетчики арендаторов</a></li>
                                                <li><a href="#">Собственное потребление</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                @if(\App\User::hasRole('admin') || \App\User::hasRole('guard'))
                                <li><a><i class="fa fa-table" aria-hidden="true"></i> Посещение <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('works') }}">Присутствие на выставке</a></li>
                                        <li><a href="{{ route('visits') }}">Посещение выставки</a></li>
                                    </ul>
                                </li>
                                @endif
                                @if(\App\User::hasRole('admin') || \App\User::hasRole('energy'))
                                <li><a><i class="fa fa-lightbulb-o" aria-hidden="true"></i> Энергоучет <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('main-counter') }}">Счетчики общие</a></li>
                                        <li><a href="{{ route('own-counter') }}">Собственные счетчики</a></li>
                                        <li><a href="{{ route('renters-counter') }}">Счетчики арендаторов</a></li>
                                        <li><a href="#">Начальные показания <span class="fa fa-chevron-down"></span></a>
                                            <ul class="nav child_menu">
                                                <li><a href="#">Счетчики общие</a></li>
                                                <li><a href="#">Собственные счетчики</a></li>
                                                <li><a href="#">Счетчики арендаторов</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Расчет потребления</a></li>
                                    </ul>
                                </li>
                                @endif
                                @if(\App\User::hasRole('admin'))
                                    <li><a><i class="fa fa-cogs" aria-hidden="true"></i> Настройки <span
                                                class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="{{ route('users') }}">Пользователи</a></li>
                                            <li><a href="{{ route('roles') }}">Роли</a></li>
                                            <li><a href="{{ route('actions') }}">Разрешения</a></li>
                                            <li><a href="{{ route('ecounters') }}">Счетчики общие</a></li>
                                            <li><a href="{{ route('own-ecounters') }}">Собственные счетчики</a></li>
                                            <li><a href="{{ route('places') }}">Территории</a></li>
                                            <li><a href="{{ route('describers') }}">Подписчики</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ route('connections') }}"><i class="fa fa-globe" aria-hidden="true"></i> Подключения к
                                            интернет </a></li>
                                @endif
                                @if(\App\User::hasRole('admin'))
                                <li><a><i class="fa fa-book" aria-hidden="true"></i>Справочники <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('cities') }}">Города</a></li>
                                        <li><a href="{{ route('materials') }}">Материалы</a></li>
                                        <li><a href="{{ route('tvsources') }}">Медиа</a></li>
                                    </ul>
                                </li>
                                @endif
                                @if(\App\User::hasRole('admin'))
                                    <li><a><i class="fa fa-calculator" aria-hidden="true"></i>Расходы ИТ <span
                                                class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="{{ route('unit-groups') }}">Структурные подразделения</a></li>
                                            <li><a href="{{ route('expenses') }}">Статьи расходов</a></li>
                                            <li><a href="{{ route('suppliers') }}">Поставщики</a></li>
                                            <li><a href="{{ route('costs') }}">Расходы</a></li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Настройки" href="#">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Затраты ИТ" href="#">
                            <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Журнал событий" href="#">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        </a>
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
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>

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
                                    <li><a href="javascript:;"> Профиль</a></li>
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
        @section('tile_widget')
            <!-- top tiles -->
                <div class="row top_tiles">
                    <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="tile-stats">
                            <div class="icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                            <div class="count">{{ empty($kassa) ? '0' : $kassa }} чел.</div>
                            <h3>Посещение выставки</h3>
                            <p><a href="#">подробнее</a></p>
                        </div>
                    </div>
                    <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="tile-stats">
                            <div class="icon"><i class="fa fa-video-camera" aria-hidden="true"></i></div>
                            <div class="count">{{ empty($coming) ? '0' : $coming }} <i class="fa fa-arrow-circle-down"
                                                                                       aria-hidden="true"></i> {{ empty($coming) ? '0' : $coming }}
                                <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></div>
                            <h3>Счетчики посетителей </h3>
                            <p>подробнее</p>
                        </div>
                    </div>
                    <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="tile-stats">
                            <div class="icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
                            <div class="count">{{ empty($expense) ? '0' : $expense }} час.</div>
                            <h3>Работа домов </h3>
                            <p>подробнее</p>
                        </div>
                    </div>
                    <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="tile-stats">
                            <div class="icon"><i class="fa fa-lightbulb-o" aria-hidden="true"></i></div>
                            <div class="count">{{ empty($expense) ? '0' : $expense }} кВт</div>
                            <h3>Энергопотребление</h3>
                            <p>подробнее</p>
                        </div>
                    </div>
                </div>
                <!-- /top tiles -->
        @endsection
        @yield('tile_widget')

        @yield('content')

        @section('footer')
            <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        Великолепные системы
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
    <!-- FastClick -->
    <script src="/js/fastclick.js"></script>
    <!-- NProgress -->
    <script src="/js/nprogress.js"></script>
    <!-- iCheck -->
{{--<script src="/js/icheck.min.js"></script>--}}


<!-- Custom Theme Scripts -->
    <script src="/js/custom.min.js"></script>

@show

@section('user_script')
@show

</body>
</html>
