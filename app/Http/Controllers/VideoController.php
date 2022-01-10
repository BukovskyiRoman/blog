<?php

namespace App\Http\Controllers;

//require 'vendor/autoload.php';

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;
use Aws\Exception\AwsException;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class VideoController extends Controller
{
    public function upload(Request $request)
    {
        $process = new Process(['sh /home/bandapixels/blog/bash.sh']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();

        $path = $request->file('video')->path();

        $s3Client = new S3Client([
           // 'profile' => 'default',
            'region' => 'eu-central-1',
            'version' => '2006-03-01'
        ]);

        $bucket = 'blog-06012022';
        $key = 'video.mp4';

        // Using stream instead of file path
        $source = fopen($path, 'w+');

        $uploader = new ObjectUploader(
            $s3Client,
            $bucket,
            $key,
            $source
        );

        do {
            try {
                $result = $uploader->upload();
                if ($result["@metadata"]["statusCode"] == '200') {
                    print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');
                }
                print($result);
            } catch (MultipartUploadException $e) {
                rewind($source);
                $uploader = new MultipartUploader($s3Client, $source, [
                    'state' => $e->getState(),
                ]);
            }
        } while (!isset($result));

        fclose($source);
        return redirect()->back();
    }
}
