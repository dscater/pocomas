@extends('layouts.login')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <div class="login-box border border-dark">
        <div class="login-logo bg-red mb-0 border-bottom border-dark p-3">
            <a href="">{{ App\RazonSocial::first()->nombre }}</a>
            <img src="{{ asset('imgs/' . App\RazonSocial::first()->logo) }}" alt="Logo">
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body bg-red">
                <p class="login-box-msg font-weight-bold">Iniciar Sesión</p>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" autofocus
                            placeholder="Usuario">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('name')
                            <span class="invalid-feedback" style="display:block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" style="display:block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        @if (session('error_c'))
                            <div class="alert alert-warning text-center"><button type="button" class="close" data-dismiss="alert">&times;</button>La cuenta a la que esta  intentando acceder fue inhabilitada o eliminada. Para mas información comuniquese con un administrador.</div>
                        @endif
                        <!-- /.col -->
                        @if (session('error_session'))
                            <div class="alert bg-gray text-center"><button type="button" class="close" data-dismiss="alert">&times;</button>{{session('error_session')}}</div>
                        @endif
                        <div class="col-12 acciones">
                            <button type="submit" class="btn btn-info btn-danger bg-red border border-dark">Acceder</button>
                            <a href="{{ route('exposicion') }}" class="btn btn-info btn-default bg-dark border-dark">Ver Exposición de
                                Productos</a>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
