<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/icono-sistema.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight" style="color: white">PRESUPUESTO</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            @can('seccion.roles.y.permisos')
             <li class="nav-item">

                 <a href="#" class="nav-link">
                    <i class="far fa-edit"></i>
                    <p>
                        Roles y Permisos
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Roles</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Permisos</p>
                        </a>
                    </li>

                </ul>
             </li>
             @endcan


             @can('seccion.configuraciones')
                <li class="nav-item">

                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Configuración
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        @can('seccion.configuracion.basepresupuesto')
                        <li class="nav-item">
                            <a href="{{ route('admin.basepresupuesto.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Base de Presupuesto</p>
                            </a>
                        </li>
                        @endcan

                            @can('seccion.configuracion.varias.vistas')
                        <li class="nav-item">
                            <a href="{{ route('admin.rubro.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rubro</p>
                            </a>
                        </li>
                            @endcan

                            @can('seccion.configuracion.varias.vistas')
                        <li class="nav-item">
                            <a href="{{ route('admin.cuenta.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cuentas</p>
                            </a>
                        </li>
                            @endcan

                            @can('seccion.configuracion.varias.vistas')
                        <li class="nav-item">
                            <a href="{{ route('admin.objespecifico.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Objeto Específico</p>
                            </a>
                        </li>
                            @endcan

                            @can('seccion.configuracion.unidadmedida')
                        <li class="nav-item">
                            <a href="{{ route('admin.unidades.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Unidad de Medida</p>
                            </a>
                        </li>
                            @endcan

                            @can('seccion.configuracion.varias.vistas')
                        <li class="nav-item">
                            <a href="{{ route('admin.departamento.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Departamentos</p>
                            </a>
                        </li>
                            @endcan

                            @can('seccion.configuracion.varias.vistas')
                        <li class="nav-item">
                            <a href="{{ route('admin.anio.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Año de Presupuesto</p>
                            </a>
                        </li>
                            @endcan

                    </ul>
                </li>
             @endcan


             @can('url.presupuesto.crear.index')
                <li class="nav-item">

                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Mi Presupuesto
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.crear.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.editar.presupuesto.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Editar</p>
                            </a>
                        </li>

                    </ul>
                </li>
             @endcan

             @can('url.encargada.presupuesto.index')
                <li class="nav-item">

                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Presupuestos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.ver.presupuestos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Revisar</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.generar.presupuestos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Generar</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endcan

            </ul>
        </nav>




    </div>
</aside>






