@extends ('layouts.app')  

	@section('navi')		
	    <li class="breadcrumb-item"><a href="/">Главная</a></li>	    
	@stop                    
	
	@section('content')
		<h1>Добро пожаловать</h1>

		<a href="/articles/create">Создать статью</a>

			<div class="row mb-2">
			@foreach ($articles_old as $article_old)

				<div class="col-md-6">
					<div class="card flex-md-row mb-4 box-shadow h-md-250">
						@if ($article_old->img === 'none')
							<img class="card-img-right flex-auto d-none d-md-block" src="/img/none.jpg" width="150px">
						@else
							<img class="card-img-right flex-auto d-none d-md-block" src="{{ asset('/storage/')}}/uploads/article/{!! $article_old->id !!}/{!! $article_old->img !!}" width="150px">
						@endif

						<div class="card-body d-flex flex-column align-items-start">
							<a class="d-inline-block mb-2 text-primary" href="{{ action('ArticleController@show', [$article_old->id]) }}">{!! $article_old->title !!}</a>
							<div class="mb-1 text-muted">{!! $article_old->user->name !!}</div>
						</div>
					</div>
				</div>	

			@endforeach
	</div>	
	@stop