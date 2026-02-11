<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class TestS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to S3/MinIO bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing S3 Connection...');

        $disk = 's3';
        $config = config("filesystems.disks.$disk");

        $this->info("Endpoint: " . ($config['endpoint'] ?? 'Not Set'));
        $this->info("Bucket: " . ($config['bucket'] ?? 'Not Set'));
        $this->info("Region: " . ($config['region'] ?? 'Not Set'));
        $this->info("Using Path Style: " . ($config['use_path_style_endpoint'] ? 'Yes' : 'No'));

        if (empty($config['key']) || empty($config['secret']) || empty($config['bucket']) || empty($config['region'])) {
            $this->error('Missing S3 configuration! Please check your .env file.');
            if (empty($config['bucket']))
                $this->warn('- AWS_BUCKET is missing');
            if (empty($config['key']))
                $this->warn('- AWS_ACCESS_KEY_ID is missing');
            if (empty($config['secret']))
                $this->warn('- AWS_SECRET_ACCESS_KEY is missing');
            if (empty($config['region']))
                $this->warn('- AWS_DEFAULT_REGION is missing');
            return;
        }

        // Force throw exceptions to get the real error
        config(["filesystems.disks.$disk.throw" => true]);

        try {
            $this->info('Attempting to write file...');
            // Attempt to write a file
            $testFile = 'connection-test-' . time() . '.txt';
            $success = Storage::disk($disk)->put($testFile, 'This is a test file.');

            if ($success) {
                $this->info("Successfully wrote test file: $testFile");
            } else {
                $this->error("Failed to write test file (unknown reason, success=false)");
                return;
            }

            // Attempt to read it back
            if (Storage::disk($disk)->exists($testFile)) {
                $this->info("Successfully verified file existence: $testFile");
                $url = Storage::disk($disk)->url($testFile);
                $this->info("Generated URL: $url");
            } else {
                $this->error("File was written but could not be found!");
            }

            // Cleanup
            Storage::disk($disk)->delete($testFile);
            $this->info("Test file deleted.");

            $this->info('Connection Successful!');

        } catch (Exception $e) {
            $this->error('Connection Failed!');
            $this->error('Error Message: ' . $e->getMessage());
        }
    }
}
