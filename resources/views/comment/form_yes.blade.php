	{!! Form::open(['url' => 'comments', 'class' => ""]) !!}

		<div class="form-group">			
			{{ Form::textarea('body', null, ['class' => 'form-control form-control-lg', 'rows' => '5', 'cols' =>  120, 'placeholder' => 'Комментарий', 'id' => 'description']) }}
		</div>	

		{!! Form::hidden('article_id', $article->id) !!}			

		<div class="form-group">
			{!! Form::submit('Отправить комментарий', ['class' => 'btn btn-primary form-control']) !!}	
		</div>

	{!! Form::close() !!}

	@include('errors.list')	