<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));

        $reports = collect();
        $letters = collect();

        if (strlen($query) >= 2) {
            $reports = Report::with(['user', 'attachments'])
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->latest()
                ->take(10)
                ->get();

            $letters = Letter::with('user')
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('reference_number', 'like', "%{$query}%")
                        ->orWhere('correspondent', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->latest('letter_date')
                ->take(10)
                ->get();
        }

        return view('search.index', compact('query', 'reports', 'letters'));
    }
}
