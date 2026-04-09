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

        if ($items->isEmpty()) {
            $items = collect([
                (object) ['category' => 'Vocabulary', 'title' => 'Oxford Word Skills - Basic', 'description' => 'Everyday vocabulary drills for speaking and reading practice.', 'file_path' => 'https://www.oxfordlearnersbookshelf.com/', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
                (object) ['category' => 'Grammar', 'title' => 'English Grammar in Use', 'description' => 'Core grammar reference with self-study exercises.', 'file_path' => 'https://www.cambridge.org/', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
                (object) ['category' => 'Reading', 'title' => 'Penguin Readers Starter Pack', 'description' => 'Short graded readers for fluency and comprehension.', 'file_path' => 'https://www.penguinreaders.co.uk/', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
                (object) ['category' => 'IELTS', 'title' => 'Official IELTS Practice Materials', 'description' => 'Practice sets for reading, listening, writing and speaking.', 'file_path' => 'https://ielts.org/', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
                (object) ['category' => 'Listening', 'title' => 'BBC Learning English Collection', 'description' => 'Listening and pronunciation exercises for daily study.', 'file_path' => 'https://www.bbc.co.uk/learningenglish', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
                (object) ['category' => 'Kids', 'title' => 'English for Kids Story Pack', 'description' => 'Simple stories and visual vocabulary sets for younger learners.', 'file_path' => 'https://learnenglishkids.britishcouncil.org/', 'instructor' => (object) ['name' => 'LinguFranca Team'], 'is_external' => true],
            ]);
            $categories = $items->pluck('category')->filter()->unique()->values();
            $filteredItems = $selectedCategory !== ''
                ? $items->where('category', $selectedCategory)
                : $items;
        }

        return view('frontend.student-dashboard.library.index', [
            'items' => $items,
            'categories' => $categories,
            'filteredItems' => $filteredItems,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
