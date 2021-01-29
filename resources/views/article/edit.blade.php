@extends('layouts.app')

@section('content')
	<h1>Редактирование статьи</h1>

	{!! Form::model($article, ['method' => 'PATCH', 'action' => ['ArticleController@update', $article->id]]) !!}

		@include ('article.form')

		<div class="form-group">
			{!! Form::submit('Редактировать статью',['class' => 'btn btn-primary form-control']) !!}	
		</div>

	{!! Form::close() !!}

	@include('errors.list')

@endsection