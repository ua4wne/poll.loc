<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>{{ $title ?? '' }}</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <!-- dataTables -->
    <link href="/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/custom.min.css" rel="stylesheet">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        @section('left_menu')
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="{{ route('main') }}" class="site_title"><i class="fa fa-paw"></i> <span>Финплан</span></a>
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

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a href="{{ route('main') }}"><i class="fa fa-home"></i> Рабочий стол </a></li>
                                <li><a><i class="fa fa-university"></i> Банк и касса <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Банковские выписки</a></li>
                                        <li><a href="#">Кассовые документы</a></li>
                                        <li><a href="#">Авансовые отчеты</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-credit-card"></i> Покупки и продажи <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Реализация (продажи)</a></li>
                                        <li><a href="#">Поступление (покупки)</a></li>
                                        <li><a href="#">Номенклатура</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-users"></i> Сотрудники и зарплата <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        {{--<li><a href="#">Сотрудники</a></li>--}}
                                        <li><a href="#">Физические лица</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-sitemap"></i> Наши юр. лица <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Организации</a></li>
                                        <li><a href="#">Подразделения</a></li>
                                        <li><a href="#">Банковские счета</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-address-card"></i>Контрагенты <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Группы контрагентов</a></li>
                                        <li><a href="#">Договоры</a></li>
                                        <li><a href="#">Физлица</a></li>
                                        <li><a href="#">Юрлица</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-bar-chart-o"></i> Отчеты <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Кассовая книга</a></li>
                                        <li><a href="#">Карточка счета</a></li>
                                        <li><a href="#">Оборотно-сальдовая ведомость по счету</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a><i class="fa fa-address-book"></i> Справочники <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Валюты</a></li>
                                        <li><a href="#">Банки</a></li>
                                        <li><a href="#">Виды операции</a></li>
                                        <li><a href="#">Виды договоров</a></li>
                                        <li><a href="#">Виды расчетов</a></li>
                                        <li><a href="#">План счетов бухучета</a></li>
                                    </ul>
                                </li>
                                {{--@if(\App\User::hasRole('admin'))
                                <li><a><i class="fa fa-cog"></i> Настройки <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="#">Пользователи</a></li>
                                        <li><a href="#">Роли</a></li>
                                        <li><a href="#">Разрешения</a></li>
                                        </li>
                                    </ul>
                                </li>
                                @endif--}}
                            </ul>
                        </div>

                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Settings">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
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
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
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
                                    <li><a href="{{ route('logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
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
                        <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="tile-stats">
                                <div class="icon"><i class="fa fa-rub"></i></div>
                                <div class="count">Касса</div>
                                <h3>Остаток: {{ empty($kassa) ? '0' : $kassa }} (руб.)</h3>
                                <p><a href="#">подробнее</a></p>
                            </div>
                        </div>
                        <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="tile-stats">
                                <div class="icon"><i class="fa fa-arrow-down"></i></div>
                                <div class="count">Приход</div>
                                <h3>Итого: {{ empty($coming) ? '0' : $coming }} (руб.)</h3>
                                <p>за текущий год</p>
                            </div>
                        </div>
                        <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <div class="tile-stats">
                                <div class="icon"><i class="fa fa-arrow-up"></i></div>
                                <div class="count">Расход</div>
                                <h3>Итого: {{ empty($expense) ? '0' : $expense }} (руб.)</h3>
                                <p>за текущий год</p>
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
