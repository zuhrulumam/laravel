<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    public function test_file_upload_page_is_accessible()
    {
        $response = $this->get('/upload');
        $response->assertStatus(200);
        $response->assertSee('File Upload');
    }

    public function test_file_can_be_uploaded_locally()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('document.jpg');

        $response = $this->post('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Assert the file was stored...
        Storage::disk('local')->assertExists('uploads/' . $file->hashName());
    }
}
