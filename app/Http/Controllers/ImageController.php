<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUplaodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(ImageUplaodRequest $request){
        $file = $request->file('image');
        $name = Str::random(10);
        $url = Storage::putFileAs('images',$file,$name.'.'.$file->extension());

        //Modificar config/filesystems
        return[
            'url'=> env('APP_ENV').'/'. $url
        ];
//        return env('APP_URL');
    }
}
