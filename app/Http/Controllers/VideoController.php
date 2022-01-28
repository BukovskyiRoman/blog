<?php

namespace App\Http\Controllers;

//require 'vendor/autoload.php';

use Aws\Exception\MultipartUploadException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;
use Aws\Exception\AwsException;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VideoController extends Controller
{
    public function upload(Request $request)
    {
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

        $this->encodeVideo();
        return redirect()->back();
    }

    public function encodeVideo()
    {
        $lowBitrate = (new X264())->setKiloBitrate(250);
        $midBitrate = (new X264())->setKiloBitrate(500);
        $highBitrate = (new X264())->setKiloBitrate(1000);

        \ProtoneMedia\LaravelFFMpeg\Support\FFMpeg::open('video.mp4')
            ->exportForHLS()
            ->setSegmentLength(10) // optional
            ->setKeyFrameInterval(48) // optional
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->toDisk('public')
            ->save('videos/adaptive_video.m3u8');
    }
}
