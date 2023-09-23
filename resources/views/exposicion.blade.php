<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>COAX</title>
    <link rel="stylesheet" href="{{ asset('template/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/exposicion.css')}}">
</head>

<body>

    <div class="slideshow">
		<ul class="slider">
            {!! $html !!}
		</ul>

		<ol class="pagination">
			
		</ol>
	
		<div class="left">
			<span class="fa fa-chevron-left"></span>
		</div>

		<div class="right">
			<span class="fa fa-chevron-right"></span>
		</div>

	</div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('template/AdminLTE-3.0.5/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('template/AdminLTE-3.0.5/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script
        src="{{ asset('template/AdminLTE-3.0.5/plugins/overlayScrollbars/js/jquery.') }}overlayScrollbars.min.js">
    </script>
    <script src="{{asset('js/exposicion.js')}}"></script>

</body>

</html>
