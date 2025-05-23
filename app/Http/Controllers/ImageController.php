<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function showImage($filename)
    {
        $path = storage_path("app/private/public/images/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        }

        return response()->file(public_path('img/box-icon.jpg'));
    }


    public function showQrCode($filename)
    {
        $path = storage_path("app/private/public/barcodes/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404, 'Barcode Code not found.');
        }
    }
}
