<li class="nav-item @if (request()->is('ventas*') || request()->is('cuenta_cobrars*')) menu-is-opening menu-open active @endif">
    <a href="#" class="nav-link">
        <i class="nav-icon far fa-list-alt"></i>
        <p>Ventas <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('ventas.index') }}" class="nav-link @if (request()->is('ventas')) active @endif">
                <i class="nav-icon far fa-circle"></i>
                <p>Ventas</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('ventas.create') }}" class="nav-link @if (request()->is('ventas/create*')) active @endif">
                <i class="nav-icon far fa-circle"></i>
                <p>Nueva Venta</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('cuenta_cobrars.index') }}"
                class="nav-link @if (request()->is('cuenta_cobrars')) active @endif">
                <i class="nav-icon far fa-circle"></i>
                <p>Lista de cuentas por cobrar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('cuenta_cobrars.create') }}"
                class="nav-link @if (request()->is('cuenta_cobrars/create*')) active @endif">
                <i class="nav-icon far fa-circle"></i>
                <p>Registrar cuentas por cobrar</p>
            </a>
        </li>
    </ul>
</li>

@if (Auth::user()->caja)
    <li class="nav-item">
        <a href="{{ route('ingreso_cajas.index', Auth::user()->caja->caja_id) }}"
            class="nav-link {{ request()->is('cajas*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cash-register"></i>
            <p>Caja</p>
        </a>
    </li>
@endif

<li class="nav-item">
    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Clientes</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('cierre_cajas.index') }}" class="nav-link {{ request()->is('cierre_cajas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-list-alt"></i>
        <p>Cierre de Cajas</p>
    </a>
</li>
