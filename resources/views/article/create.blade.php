@extends('layouts.app')

@section('content')
	<h1>Создание статьи (АДМИНКА)</h1>

	{!! Form::open(['url' => 'articles']) !!}

		@include ('article.form')

		<div class="form-group">
			{!! Form::submit('Создать', ['class' => 'btn btn-primary form-control']) !!}	
		</div>

	{!! Form::close() !!}

	@include('errors.list')

@endsection