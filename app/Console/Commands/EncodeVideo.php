<?php

namespace App\Console\Commands;

use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;
use  ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class EncodeVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encode:video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encode video for hls';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::open('video.mp4')
            ->exportForHLS()
            ->setSegmentLength(10) // optional
            ->setKeyFrameInterval(48) // optional
            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
            ->addFormat($highBitrate)
            ->onProgress(function ($progress) {
                $this->info("Progress->  {$progress}  %");
            })
            ->toDisk('public')
            ->save('videos/adaptive_video.m3u8');

//        FFMpeg::open('bbb_sunflower_1080p_30fps_normal.mp4')
//            ->exportForHLS()
//            ->addFormat($lowBitrate)
//            ->addFormat($midBitrate)
//            ->addFormat($highBitrate)
//            ->save('encrypted_video.m3u8');

        $this->info('Done!');
    }
}
