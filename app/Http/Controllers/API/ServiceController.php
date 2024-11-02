<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Document;
use App\Models\Vidio;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getImage()
    {
        $images = Image::all();
        return response()->json([
            'data' => $images,
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function getDocument()
    {
        $documents = Document::all();
        return response()->json([
            'data' => $documents,
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function getVidio()
    {
        $vidios = Vidio::all();
        return response()->json([
            'data' => $vidios,
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);
        
        // Simpan file dan dapatkan path
        $path = $request->file('images')->store('images', 'public');
        
        // Buat record baru dengan array yang benar
        $image = Image::create([
            'images' => $path
        ]);
        
        // Dapatkan URL lengkap dengan domain
        $url = asset('storage/' . $image->images);
        // atau bisa juga menggunakan
        // $url = url('storage/' . $image->images);
        
        return response()->json([
            'data' => [
                'image' => $image,
                'url' => $url
            ],
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function storeDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);
        $path = $request->file('document')->store('documents', 'public');
        $document = Document::create([
            'document' => $path
        ]);
        $url = asset('storage/' . $document->document);
        return response()->json([
            'data' => [
                'document' => $document,
                'url' => $url
            ],
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function storeVidio(Request $request)
    {
        $request->validate([
            'vidio' => 'required|file|mimes:mp4,mov,avi,wmv,flv,mkv|max:40960',
        ]);
        $path = $request->file('vidio')->store('vidios', 'public');
        $vidio = Vidio::create([
            'vidio' => $path
        ]);
        $url = asset('storage/' . $vidio->vidio);
        return response()->json([
            'data' => [
                'vidio' => $vidio,
                'url' => $url
            ],
            'message' => 'success',
            'code' => 200
        ], 200);
    }
}
