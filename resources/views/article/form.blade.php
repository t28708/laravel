		<div class="form-group">
			{!! Form::label('title', 'Заголовок:') !!}
			{!! Form::text('title', 'Заголовок', ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('body', 'Текст материала:') !!}
			<redaktor :items='@json($bodyEdit)' :itemsopis='@json($BodyImgOpis)' :itemsalt='@json($BodyImgAlt)'></redaktor>
		</div>

		@if ($isAdmin === True)

			<div class="form-group"> {!! Form::text('meta_title', null, ['class' => 'form-control form-control-lg', 'placeholder' => 'Title']) !!} </div>
			<div class="form-group"> {!! Form::text('meta_description', null, ['class' => 'form-control form-control-lg', 'placeholder' => 'Description']) !!} </div>
			
		@endif		


