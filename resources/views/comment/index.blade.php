@extends ('layouts.app')

	@section('navi')
		<a href="/comments">Комментарии</a> <hr>  
	@stop

	@section('content')

		@foreach ($comments as $comment)

			<li><a href="{{ action('CommentController@show', [$comment->id]) }}">{!! $comment->title !!}</a></li>				

		@endforeach	
			
	@stop