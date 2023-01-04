<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Elasticsearch;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('q')) {
            $page = $request->get('page', 1);

            $q = $request->get('q');
            $postResponse = Elasticsearch::search([
                'index' => 'posts',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $q,
                            'fields' => [
                                'title',
                                'body'
                            ]
                        ]
                    ]
                ]
            ]);

            $postIds = array_column($postResponse['hits']['hits'], '_id');
            $posts = Post::select('id', 'title', 'body')
                ->whereIn('id', $postIds)
                ->with('author');

            $commentResponse = Elasticsearch::search([
                'index' => 'comments',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $q,
                            'fields' => [
                                'body'
                            ]
                        ]
                    ]
                ]
            ]);

            $commentIds = array_column($commentResponse['hits']['hits'], '_id');

            $results = Comment::select(['id', DB::raw('0 as title'), 'body'])
                ->whereIn('id', $commentIds)
                ->with('author')
                ->union($posts)
                ->paginate(5);



            return view('search-list', compact('results'));
        }
    }
}
