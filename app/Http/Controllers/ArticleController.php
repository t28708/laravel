<?php

namespace App\Http\Controllers;

//////////////////////////////
use Auth;
use App\Http\Requests;
use Request;
use App\Article;
use App\Place;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use App\Comment;

use Illuminate\Support\Facades\Storage; //это для Storage перенос картинки из временной папки


class ArticleController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth')->except(['index', 'show']);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function create()
    {
        $isAdmin = False; // проверка на админа - кривая, но это тестовое, можно смотреть админа через БД по полю придуманному всемогущественности
        if (Auth::id() === 1) {
            $isAdmin = True;            
        } else {
            return redirect('/no_access');
        }

        $nameImgs[] = ""; // при создании объявим пустыми эти массивы, так как картинок ещё нет (подписи и альтов тоже НЕТ)

        $bodyEditOpisNew = [];
        $bodyEditAltNew = [];
        $bodyEdit  =[];
              
        return  view('article.create' , ['bodyEdit' => $bodyEdit,'nameImgs' => $nameImgs, 'BodyImgOpis' => $bodyEditOpisNew, 'BodyImgAlt' => $bodyEditAltNew, ]);  

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function store(Requests\ArticleRequest $request) // сохранение статьи в БД и редирект её
    {   
        $body_all = ""; //тут будут храниться объеденённые данные с массива     
        
        $article = new Article($request->all());
        //dd($article);

        $imgBody = $article->body; //надо записать исходные данные
        $imgOpisanie = $article->opisanie; //надо записать исходные данные

        ////// Соединяем, добавляем теги <p>
        $article->body = array_diff($article->body, array('')); // удаляем null      
        $article->body = implode(" \r\n" , $article->body);
        
        //////записываем данные по alt и подписи и очищаем чтобы прошёл запрос
        $imgOpisanie = $article->opisanie; 
        $imgAlt = $article->alt;
        unset($article['opisanie']); 
        unset($article['alt']); 

        // сохраняем данные в БД
        $result = Auth::user()->article()->save($article); 
        
        // получаем новый элемент уже из БД. Необходимо вытащить поле body и переименовать в нём картинки
        $articleEdit = Article::OrderBy('title')->where('id', $result->id)->first();
        //dd($articleEdit);
        

        $body_all = implode(" " , $imgBody);

        $new_path = 'public/uploads/article/' . $result->id . '/'; // для storage - используем директиву public     

        foreach ($imgBody as $key=>$value) {

            if ( (strpos($value, ".jpg")) and (!strpos($value, "article")) ) {
    
                $value = str_replace('storage','public', $value);
                $value_new = basename($value);

                $value_logo = str_replace('.jpg','_logo.jpg', $value);
                $value_logo_new = basename($value_logo);   
                

                Storage::move($value, $new_path .  $value_new); // переносим из временной папки в папку с id vehicle
                Storage::move($value_logo, $new_path .  $value_logo_new); // переносим из временной папки в папку с id vehicle
                
                //dd($value_logo);   
                $imgBody[$key] = $new_path .  $value_new;
                $imgBody[$key] = str_replace('public','storage', $imgBody[$key]);
                //$imgBody_logo[$key] = $new_path .  '/' . $key . '.jpg';
            } 

        }

        ///////////////////////////////////////////////////////////////////////////////
        //формируем данные для отправки в БД  

            

        foreach ($imgBody as $key=>$value) {

            if ( (strpos($value, ".jpg")) and (strpos($value, "article")) ) {

                $value = str_replace('storage','public', $value);
                $value = str_replace('/public','public', $value); 

                if(is_null($imgAlt[$key])) {
                    $imgAlt[$key] = $imgOpisanie[$key];
                }               
               
                $got = '<div class="wp-caption aligncenter" style="width: 660px;"><img width="650px" src="' . $value .'" alt="'.htmlspecialchars($imgAlt[$key]).'"> <p id="imgOpisanie'.($key).'" class="wp-caption-text">'.$imgOpisanie[$key].'</p></div>';
                
                $imgBody[$key] = $got;

            }

        } 

        ///////////////////////////////////////////////////////////////////////////////
        //Выводим запрос для записи в БД
        
        $imgBody = implode("\r\n", $imgBody); 
        $articleEdit['body'] = $imgBody;        
        
        //Сохраняем наши изменения. 
        $articleEdit->update();
        
        return redirect('articles/' . $result->id);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function index() // отображает главную
    {
        return redirect('/'); // переброс на главную, по сути главная ЭТО эта страница
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function show($id) // передавали до этого $id
    {
        $isHolder = false; // прописываем для проверки на владельца записи - чтобы в шаблоне выдавать ссылку на редактирование

        $article = Article::with('user')->findOrFail($id);

       // dd($article);

        if (Auth::check()) { 
            if ( Auth::user()->id == $article->user->id) {
                $isHolder = true;
            }
        }
       
        ///////////////////////////////////////////////
        ///////////////////////////////////////////////
        // тащим из описания данные и бьём их на массив

        $article->body = str_replace("public/uploads/", '/storage/uploads/', $article->body);
        
        //стащил код дерева, и внедрил               

        $comments = Comment::orderby('created_at')->where('article_id', $id)->get();
        //dd($comments);

        //необходимо пройти цикл чтобы записать данные в title имя автора комментария
        foreach ($comments as $comment) {
            if (!is_null($comment['user_id'])) {
                $comment['title'] = $comment->user->name;
                unset($comment->user);  //почему-то прилетало во vue потом,                  
            }
        }

        //dd($comments);

        //////////отсортируем массив, чтобы все элементы комментариев родителя были следующими после родителя
        function CreateTree($array,$sub=0,$tab='')
        {
            //asort($array);
            $category=array();

            if($sub>0){$tab=true;}
            foreach($array as $key=>$v){
                if($sub == $v['comment_parent']){
                    $category[$v['id']] = $v;
                    $v['child'] = $tab;
                    $category += CreateTree($array,$v['id'],$tab);
                }
            }
            return $category;
        }

        $comments = CreateTree($comments);
        $comments2 = array();

        foreach($comments as $comment){
            $comments2[] = $comment;
        }


        $comments = $comments2;

        //////////отсортируем массив, чтобы все элементы комментариев родителя были следующими после родителя - тыренный код, модифицированный мной, рекурсия кАроче

        //dd($comments);

        $num_comment = (count($comments));
        

        $isAuth = false;
        if (Auth::check()) { 
            $isAuth = true; // чтобы знать какой шаблон выводить
        }

       

        return view('article.show' , ['article' => $article, 'isHolder' => $isHolder, 'num_comment' => $num_comment, 'comments' =>$comments, 'isAuth' => $isAuth,]);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      public function edit(Article $article)
    {

        $bodyEdits = $article->body; //запишем в массив, так как потом разделим     

        $bodyEdits = explode("\r\n" , $bodyEdits); // засунули всё в массив


        foreach ($bodyEdits as $bodyEdit) {

            $pos = strpos($bodyEdit, "<div");

            if ($pos !== false) { 

                $bodyEditImg = preg_replace('~^.+src="([^"]+)".+$~m',  '$1', $bodyEdit); // чистим тег div - теперь тут просто адрес картинки       
                
                $bodyEditAlt = preg_replace('~^.+alt="([^"]+)".+$~m',  '$1', $bodyEdit);
                if (strpos($bodyEditAlt, "div")) {
                    $bodyEditAlt = '';
                }
                

                $bodyEditOpis = strip_tags($bodyEdit, '<p>');; // чистим тег div

                $bodyEditOpis = preg_replace("#(</?\w+)(?:\s(?:[^<>/]|/[^<>])*)?(/?>)#ui", '', $bodyEditOpis);
                $bodyEditOpis = trim($bodyEditOpis);           
                
                $bodyEditsImgNew[] = $bodyEditImg;

                $bodyEditOpisNew[] = $bodyEditOpis;
                $bodyEditAltNew[] = $bodyEditAlt;


            } else {
                $bodyEditsImgNew[] = $bodyEdit;
                $bodyEditOpisNew[] = null;
                $bodyEditAltNew[] = null;

            }   
        }

        

        $isAdmin = False; // проверка на админа
        if (Auth::id() === 1) {
            $isAdmin = True;            
        } else {
            return redirect('/no_access'); 
        }
          


        if ( ( Auth::user()->id == $article->user->id) || ($isAdmin == True) ) {
             return  view('article.edit' , [ 'article' => $article, 'bodyEdit' => $bodyEditsImgNew, 'BodyImgOpis' => $bodyEditOpisNew, 'BodyImgAlt' => $bodyEditAltNew, 'isAdmin' => $isAdmin]);
        } else {
            return redirect('/no_access'); // это переброс на страницу логина, если пробуют подменить данные
        }

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function update(Article $article, Requests\ArticleRequest $request)
    {
        
        $tmp = $request->all();
        //dd($tmp);       
        
        $imgOpisanie = $request->opisanie; //надо записать исходные данные         
        //dd($imgOpisanie);

        $imgAlt = $request->alt; //надо записать исходные данные   
        //dd($imgAlt);

        // подчистим формат реквеста тела
        $body_all = $request->body;
        //dd($body_all);
        $body_all = array_diff($body_all, array('')); // удаляем null
        $body_all = str_replace("<p><p>", "<p>", $body_all);
        $body_all = str_replace("</p></p>", "</p>", $body_all);
        $body_all = str_replace("</p> </p>", "</p>", $body_all);


        $imgBody = $body_all; //надо записать исходные данные
        
        
        //$imgBody = implode("," , $imgBody);

        $body_all = implode(" " , $body_all);

        //dd($body_all);

        ///////////////////////////////////////////////////////////////////////////////
        //копируем файл из реквеста, файл из временной папки uploads в папку со статьей 

        $new_path = 'public/uploads/article/' . $article->id . '/'; // для storage - используем директиву public     

        foreach ($imgBody as $key=>$value) {

            if ( (strpos($value, ".jpg")) and (!strpos($value, "article")) ) {
    
                $value = str_replace('storage','public', $value);
                $value_new = basename($value);

                $value_logo = str_replace('.jpg','_logo.jpg', $value);
                $value_logo_new = basename($value_logo);   
                

                Storage::move($value, $new_path .  $value_new); // переносим из временной папки в папку с id vehicle
                Storage::move($value_logo, $new_path .  $value_logo_new); // переносим из временной папки в папку с id vehicle
                
                //dd($value_logo);   
                $imgBody[$key] = $new_path .  $value_new;
                $imgBody[$key] = str_replace('public','storage', $imgBody[$key]);
                //$imgBody_logo[$key] = $new_path .  '/' . $key . '.jpg';
            } 

        }

        ///////////////////////////////////////////////////////////////////////////////
        //проверяем содердит ли имя в папке совпадение с imgBody. Удаляем всё что нет.

        $files = Storage::files($new_path); // Все файлы в указанном каталоге
        $imgBody_new = implode(" " , $imgBody);        
        $imgBody_new = str_replace('storage','public', $imgBody_new);       
       

        foreach ($files as $key=>$value) {            

            if  ( (!strpos(trim($imgBody_new), trim(basename($value))))  and (!strpos($value, "_logo")) ) {                

                $value_logo = str_replace('.jpg','_logo.jpg', $value);

                Storage::delete($value);
                Storage::delete($value_logo);

            }           

         }

        ///////////////////////////////////////////////////////////////////////////////
        //формируем данные для отправки в БД       

        foreach ($imgBody as $key=>$value) {

            if ( (strpos($value, ".jpg")) and (strpos($value, "article")) ) {

                if(is_null($imgAlt[$key])) {
                    $imgAlt[$key] = $imgOpisanie[$key];
                }               
               

                $value = str_replace('storage','public', $value);
                $value = str_replace('/public','public', $value);                
               
                $got = '<div class="wp-caption aligncenter" style="width: 660px;"><img width="650px" src="' . $value .'" alt="'.htmlspecialchars($imgAlt[$key]).'"> <p id="imgOpisanie'.($key).'" class="wp-caption-text">'.$imgOpisanie[$key].'</p></div>';
                
                $imgBody[$key] = $got;

            }

        } 

        ///////////////////////////////////////////////////////////////////////////////
        //Выводим запрос для записи в БД

        
        $imgBody = implode("\r\n", $imgBody);   
        
        $tmp['body'] = $imgBody;
        unset($tmp['opisanie']); 
        unset($tmp['alt']); 

        $article->update($tmp);   
       
        return redirect('articles/' . $article->id);
    }
}