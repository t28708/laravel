@extends ('layouts.app')

@section('meta_title'){!!$article->meta_title!!}@endsection

@section('meta_description'){!!$article->meta_description!!}@endsection

	@section('navi')		
	    <li class="breadcrumb-item"><a href="/articles">Статьи</a></li>	    
	@stop

	@section('edit')
		@if ($isHolder === True)
			
				<a class="badge-warning" href="/articles/{!! $article->id !!}/edit">Редактировать</a>
			
		@endif
	@stop	

	@section('content')

	<span class="form_comment"><a class="green" href="#comment_url"><i class="fa fa-comments-o" aria-hidden="true"></i> {!! $num_comment !!}</a></span>

	<h1>{!! $article->title !!}</h1>
	
	<div class="article_body">
		{!! $article->body !!}
	</div>

	@if ($isHolder === True)
		<a class="badge-warning" href="/articles/{!! $article->id !!}/edit">Редактировать</a>
	@endif

				<div class="comments-area">
                     <span id="comment_url" class="comment-h">Обсуждение</span>
                     @foreach ($comments as $comment)
                        @if (is_null($comment->comment_parent))
                           <div class="comment-list">
                        @else
                           <div class="comment-list reply_comment">
                        @endif
                        <div id="comment_url{!!$comment->id!!}" class="single-comment justify-content-between d-flex" >
                           <div class="user justify-content-between d-flex">
                              <div class="thumb">
                                 <img src="/avatar/1.png" alt="">
                              </div>
                              <div class="desc">
                                 @if (isset($comment->user->name))
                                       <span class="font-weight-bold"><a href="/profile/{!!$comment->user->id!!}">{!! $comment->user->name !!}</a></span>
                                       @else
                                       <span class="font-weight-bold">{!! $comment->title !!}</span>
                                       @endif
                                       <span class="date">{!! $comment->created_at !!} </span>
                                 <p class="comment">{!! $comment->body !!}</p>
                                 <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                       
                                       
                                       <span class="font-italic small"><a href="#" onclick='openForm({!!$comment->id!!}); return false;'>Ответить</a> </span>
                                                                  
                                    </div>
                                 </div>

                                 
                                 <div id='form-wrap{!!$comment->id!!}' style="display: none">
                                    {!! Form::open(['url' => 'comments', 'class' => ""]) !!}

                                       @if ($isAuth === False)
                                          <div class="form-group">        
                                             {!! Form::text('title', null, ['class' => 'form-control form-control-lg', 'placeholder' => 'Имя']) !!}
                                          </div> 
                                       @endif  

                                       <div class="form-group">         
                                          {{ Form::textarea('body', null, ['class' => 'form-control form-control-lg', 'rows' => '5', 'cols' =>  120, 'placeholder' => 'Комментарий', 'id' => 'description']) }}
                                       </div>   

                                       {!! Form::hidden('article_id', $article->id) !!} 
                                       {!! Form::hidden('comment_parent', $comment->id) !!}      

                                       <div class="form-group">
                                          {!! Form::submit('Отправить комментарий', ['class' => 'btn btn-primary form-control']) !!}   
                                       </div>
                                    {!! Form::close() !!}
                                 </div>

                                 
                                 
                              </div>
                           </div>
                        </div>
                     </div>
                     @endforeach
                  </div>

                  <div class="mt-4 mb-4">
                     <span class="comment-h2">Добавить комментарий</span>
                     <div class="form_comment">
                        @if ($isAuth === True)
                           @include ('comment.form_yes')
                        @else
                           @include ('comment.form_no')
                        @endif
                     </div>
                  </div>

			<script type="application/javascript">
         function openForm(id_comm) {  
            per =  'form-wrap'+id_comm;
            console.log(per);

            var formWrap = document.getElementById(per);
            console.log(formWrap);
            formWrap.style.display = "block" ;            
         }
      </script>

	

	 

	
			
	@stop