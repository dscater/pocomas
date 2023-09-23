@if (count($imagenes) > 0)
    @foreach ($imagenes as $img)
        <div class="imagen">
            <div class="img">
                <img src="{{ asset('imgs/galerias/'.$img->imagen) }}" alt="Productp">
            </div>
            <div class="info">
                {{date('d/m/Y',strtotime($img->fecha_registro))}}
            </div>
            <div class="acciones">
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-eliminar" data-url="{{route('galerias.destroy',$img->id)}}"><i class="fa fa-trash"></i></button>
            </div>
        </div>
    @endforeach
@else
    No se encontraron imagenes
@endif
