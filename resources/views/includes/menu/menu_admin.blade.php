<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Usuarios</p>
    </a>
</li>

<li class="nav-item @if (request()->is('cajas*') ||
    request()->is('inicio_cajas*') ||
    request()->is('cierre_cajas*') ||
    request()->is('ventas*')) menu-is-opening menu-open active @endif">
    <a href="#" class="nav-link">
        <i class="nav-icon fa fa-cash-register"></i>
        <p>Cajas <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('cajas.index') }}" class="nav-link @if (request()->is('cajas*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
                <p>Cajas</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ventas.index') }}" class="nav-link @if (request()->is('ventas*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
                <p>Ventas</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('caja_centrals.index') }}" class="nav-link {{ request()->is('caja_centrals*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cash-register"></i>
        <p>Caja Central</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('conceptos.index') }}" class="nav-link {{ request()->is('conceptos*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Conceptos</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('mermas.index') }}" class="nav-link {{ request()->is('mermas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Mermas</p>
    </a>
</li>

<li class="nav-item @if (request()->is('productos*') || request()->is('ingreso_productos*')|| request()->is('lote_productos*') || request()->is('galerias*')) menu-is-opening menu-open active @endif">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-box"></i>
        <p>Productos <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('productos.index') }}" class="nav-link @if (request()->is('productos*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
                <p>Productos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('lote_productos.index') }}"
                class="nav-link @if (request()->is('lote_productos*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
                <p>Lotes de Productos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ingreso_productos.create') }}"
                class="nav-link @if (request()->is('ingreso_productos*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
                <p>Ingreso de Productos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('galerias.index') }}" class="nav-link @if (request()->is('galerias*')) active @endif">
                <i class="nav-icon fa fa-chevron-right"></i>
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
    <a href="{{ route('cuenta_cobrars.index') }}" class="nav-link {{ request()->is('cuenta_cobrars*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list"></i>
        <p>Cuentas por cobrar</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('cuenta_pagars.index') }}" class="nav-link {{ request()->is('cuenta_pagars*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Cuentas por pagar</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Clientes</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('razon_social.index') }}" class="nav-link {{ request()->is('razon_social*') ? 'active' : '' }}">
        <i class="nav-icon fa fa-hospital"></i>
        <p>Razón social</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>Reportes</p>
    </a>
</li>
