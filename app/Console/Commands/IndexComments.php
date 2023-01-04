<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Console\Command;
use Elasticsearch;

class IndexComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $comments = Comment::all();
        foreach ($comments as $comment) {
            try {
                $return = Elasticsearch::index([
                    'id' => $comment->id,
                    'index' => 'comments',
                    'body' => [
                        'body' => $comment->body
                    ]
                ]);
                $this->info("Comment id = $comment->id was successfully indexed");
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }
    }
}
