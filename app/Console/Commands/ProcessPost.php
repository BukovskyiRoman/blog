<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class ProcessPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:process {post}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post process status';

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

        logger('add post id ' . $this->argument('post')->id);          //todo +
        $this->info($this->argument('post')->id);

        $count = [1, 2, 3];
        $this->output->progressStart(count($count));

        for ($i = 0; $i < count($count); $i++) {
            sleep(1);
            logger('add post ' . $i);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        return Command::SUCCESS;
    }
}
