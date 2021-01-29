<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image; //960 * 540 g -74 ya - 201

Image::configure(array('driver' => 'imagick')); // стандартный драйвер делает артефакты

class ImageController extends Controller
{
    public function upload(Request $request) // отображает главную
    {   
        $saveimg = $request->all();   

        $rand_adres = date('mdYHis') . uniqid();
        $rand_adres_logo = $rand_adres . '_logo';  
        $path = storage_path('app/public/uploads/' . $rand_adres. ".jpg"); // создаём путь до папки
        $path_logo = storage_path('app/public/uploads/' . $rand_adres_logo. ".jpg"); // создаём путь до папки

        $img = Image::make($request->file('imagev'));
        $img_logo = Image::make($request->file('imagev')); 

        // prevent possible upsizing
        $img->resize(660, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($path);

        // LOGO
        $img_logo->resize(328, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img_logo->save($path_logo);
        
        return response()->json($rand_adres);    
    }

    public function delete(Request $request)
    {   
        $delimg = $request->all();

        $path = '/public/uploads/' . $delimg['ii']; // создаём путь до папки, именно так - Storage отличается от Image

        $path_logo = '/public/uploads/' . $delimg['ii']; // создаём путь до папки, именно так - Storage отличается от Image
        $path_logo = str_replace('.jpg', '_logo.jpg', $path_logo);
       
        Storage::delete($path);
        Storage::delete($path_logo);        

       
    }

    public function uploadPlace(Request $request) // отображает главную
    {   
        $saveimg = $request->all();   

        $rand_adres = date('mdYHis') . uniqid();  
        $path = storage_path('app/public/uploads/' . $rand_adres. ".jpg"); // создаём путь до папки

        $img = Image::make($request->file('imagev'));
        $img->resize(960, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });    
        $img->resizeCanvas(960,540);
        $img->save($path);      
        
        return response()->json($rand_adres);    
    }

    public function deletePlace(Request $request)
    {   
        $delimg = $request->all();

        $path = '/public/uploads/' . $delimg['ii']. ".jpg"; // создаём путь до папки, именно так - Storage отличается от Image
       
        Storage::delete($path);      

       
    }
}
