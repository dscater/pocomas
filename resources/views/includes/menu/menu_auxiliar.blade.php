<li class="nav-item">
    <a href="{{ route('caja_centrals.index') }}" class="nav-link {{ request()->is('caja_centrals*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cash-register"></i>
        <p>Caja Central</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('cajas.index') }}" class="nav-link {{ request()->is('cajas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cash-register"></i>
        <p>Cajas</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('cuenta_pagars.index') }}" class="nav-link {{ request()->is('cuenta_pagars*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Cuentas por pagar</p>
    </a>
</li>


<li class="nav-item @if (request()->is('productos*') || request()->is('ingreso_productos*') || request()->is('galerias*')) menu-is-opening menu-open active @endif">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-box"></i>
        <p>Productos <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('productos.index') }}" class="nav-link @if (request()->is('productos*')) active @endif">
                <i class="nav-icon fa fa-angle-right"></i>
                <p>Productos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ingreso_productos.index') }}"
                class="nav-link @if (request()->is('ingreso_productos*')) active @endif">
                <i class="nav-icon fa fa-angle-right"></i>
                <p>Ingreso de Productos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('galerias.index') }}" class="nav-link @if (request()->is('galerias*')) active @endif">
                <i class="nav-icon fa fa-angle-right"></i>
                <p>Exposición de Productos</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('proveedors.index') }}" class="nav-link {{ request()->is('proveedors*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Proveedores</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('mermas.index') }}" class="nav-link {{ request()->is('mermas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Mermas</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('cuenta_cobrars.index') }}" class="nav-link {{ request()->is('cuenta_cobrars*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Lista de cuentas por cobrar</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Clientes</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Reportes</p>
    </a>
</li>
