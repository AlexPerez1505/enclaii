@extends('layouts.app')

@section('content')
<br>
<br>
<br>
<h2>Proceso Finalizado</h2>
<p>Todos los procesos han sido completados y ya esta disponible para stock.</p>
<a href="{{ route('inventario') }}">Volver al Inventario</a>
@endsection
