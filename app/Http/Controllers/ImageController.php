<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function showImage($filename)
    {
        $path = storage_path("app/private/images/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        }

        // Fallback ke image default jika tidak ditemukan
        return response()->file(public_path('img/box-icon.jpg'));
    }


    public function showQrCode($filename)
    {
        $path = storage_path("app/private/public/qr/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404, 'QR Code not found.');
        }
    }
}
