<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: white"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav">
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link" style="color: white">{{ $departamento }}</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-cogs" style="color: white"></i>
                <span class="hidden-xs" style="color: white">{{ $user->nombre }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">

                <div class="dropdown-divider"></div>

                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                    document.getElementById('frm-logout').submit();" class="dropdown-item"> <i class="fas fa-sign-out-alt"></i></i></i> Cerrar Sesión</a>

                <form id="frm-logout" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </li>



    </ul>
</nav>
