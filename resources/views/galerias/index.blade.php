@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/galerias/index.css')}}">
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Ingresos Productos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
                    <li class="breadcrumb-item active">Ingresos Productos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Información</h4>
                        {!! Form::model($galeria,['route'=>['galerias.update',$galeria->id],'method'=>"put",'files'=>true]) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nombre Galería*</label>
                                    {{Form::text('nombre',null,['class'=>'form-control','required'])}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo*</label>
                                    {{Form::select('tipo',[
                                        '' => 'Seleccione...',
                                        '1X2' => '1X2',
                                        '2X4' => '2X4',
                                    ],null,['class'=>'form-control','required'])}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    {{Form::text('descripcion',null,['class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-pen-alt"></i> Actualizar</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-info" data-toggle="modal" data-target="#modal-imgs"><i class="fa fa-upload"></i> Cargar Imágenes</button>
                                <br>
                            </div>
                        </div>
                        <h4>Imágenes</h4>
                        <div id="contenedor_imgs">
                            @include('galerias.parcial.imgs')
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
</section>

@include('galerias.modal.imgs')
@include('modal.eliminar')

@section('scripts')
<script>
    @if(session('bien'))
    mensajeNotificacion('{{session('bien')}}','success');
    @endif

    @if(session('info'))
    mensajeNotificacion('{{session('info')}}','info');
    @endif

    @if(session('error'))
    mensajeNotificacion('{{session('error')}}','error');
    @endif

    Dropzone.prototype.defaultOptions.dictInvalidFileType = "No puedes subir archivos de este tipo.";

    Dropzone.options.uploadWidget = {
        acceptedFiles: 'image/*',
        dictDefaultMessage: "Arrastra aquí tus imágenes",
        init: function() {
            this.on('success', function(file, resp){
                $('#contenedor_imgs').html(resp.html);
            });
        },
        // dictFallbackMessage = "Your browser does not support drag'n'drop file uploads.",
        // dictInvalidFileType = "No puedes subir archivos de este tipo",
        // dictCancelUpload = "Cancelar subida",
        // dictRemoveFile = "Eliminar archivo",
        // dictMaxFilesExceeded = "No puedes subir mas archivos.",
    };

    // ELIMINAR
    $('#contenedor_imgs').on('click','.imagen .acciones button',function(e){
        e.preventDefault();
        let ingreso_productos = $(this).parents('tr').children('td').eq(1).text();
        $('#mensajeEliminar').html(`¿Está seguro(a) de eliminar la imagen?`);
        let url = $(this).attr('data-url');
        $('#formEliminar').prop('action',url);
    });

    $('#btnEliminar').click(function(){
        $('#formEliminar').submit();
    });

    
</script>
@endsection

@endsection
