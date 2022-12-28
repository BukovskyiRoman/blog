<?php

namespace App\Console\Commands;

use App\Models\Post;
use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Console\Command;
use Elasticsearch;

class IndexPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:posts';

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
        $posts = Post::all();
        foreach ($posts as $post) {
            try {
                $return = Elasticsearch::index([
                    'id' => $post->id,
                    'index' => 'posts',
                    'body' => [
                        'title' => $post->title,
                        'body' => $post->body
                    ]
                ]);
                $this->info("Post id = $post->id was successfully indexed");
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }
        //$this->info("Posts were successfully indexed");
    }
}
