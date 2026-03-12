<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentLibraryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $studentId = auth()->id();
        $selectedCategory = trim((string) $request->query('category', ''));

        $items = StudentLibraryItem::query()
            ->with('instructor:id,name')
            ->where('student_id', $studentId)
            ->orderByDesc('id')
            ->get();

        $categories = $items->pluck('category')->filter()->unique()->values();
        $filteredItems = $selectedCategory !== ''
            ? $items->where('category', $selectedCategory)
            : $items;

        return view('frontend.student-dashboard.library.index', [
            'items' => $items,
            'categories' => $categories,
            'filteredItems' => $filteredItems,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
