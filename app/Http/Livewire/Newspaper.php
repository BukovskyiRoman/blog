<?php

namespace App\Http\Livewire;

use App\Models\News;
use Livewire\Component;
use Livewire\WithPagination;

class Newspaper extends Component
{
    use WithPagination;

    public function render()
    {
        $news = News::orderByDesc('created_at')->paginate(3);

        //dd($news);

        return view('livewire.news', compact('news'));
    }
}
