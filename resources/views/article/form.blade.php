		<div class="form-group">
			{!! Form::label('title', 'Заголовок:') !!}
			{!! Form::text('title', 'Заголовок', ['class' => 'form-control']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('body', 'Текст материала:') !!}
			<redaktor :items='@json($bodyEdit)' :itemsopis='@json($BodyImgOpis)' :itemsalt='@json($BodyImgAlt)'></redaktor>
		</div>


