<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/////////////////////////////////////////////////////////
use App\Article;
use Auth;
use Illuminate\Support\Facades\Storage; //это для картинок

class BlogController extends Controller
{
    public function index() // отображает главную
    {
    	//это вывод последних 6 статей на главную
    	$articles_old = Article::orderby('id', 'desc')->take(10)->get();
    	//dd($articles_old);

    	foreach ($articles_old as $article_old) { //тут лезем в папку по id, затем получаем список всех файлов

            $path_img_article = 'public/uploads/article/' . $article_old->id ;
            $files = Storage::Files($path_img_article);

            //dd($files);

            if (!empty($files)) {

                $img_article = $files[0];
                $pieces = explode("/", $img_article);            
                $img_article =  $pieces[4];
                $img_article = str_replace('.jpg','_logo.jpg', $img_article);
                //dd($img_article);

                $article_old->setAttribute('img',$img_article); // добавили новый аттрибут в коллекцию 
            } else {
                $article_old->setAttribute('img','none'); // вставим none - так как картинки нет
            } 

            //$article_num = Comment::where('article_id', $article_old->id)->count();
            //$article_old->setAttribute('num_com', $article_num); // это для количества комментариев на будущее    
        }

    	//это вывод всех статей на главную
    	$articles_all = Article::orderby('id', 'desc')->get();
    	//dd($articles_all);

        return view('index', ['articles_old' => $articles_old, 'articles_all' => $articles_all]); // переброс на главную, по сути главная ЭТО эта страница
    }
}
