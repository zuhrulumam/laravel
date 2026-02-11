<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $path = $request->file('file')->store('uploads', config('filesystems.default'));

        $url = Storage::url($path);

        return back()->with('success', "File uploaded successfully to " . config('filesystems.default') . " storage!")
            ->with('file', $url);
    }
}
