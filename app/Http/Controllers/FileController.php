<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($folder,$filename)
    {
        $filePath = storage_path("app/{$folder}/{$filename}");

        if (!Storage::exists("{$folder}/{$filename}")) {
            abort(404);
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);

        // Return the file content as response with appropriate headers
        return (new Response($fileContent, 200))
            ->header('Content-Type', 'application/pdf');
    }
}
