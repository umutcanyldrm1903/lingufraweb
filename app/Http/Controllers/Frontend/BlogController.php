<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Rules\CustomRecaptcha;
use App\Support\SeoBlogLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Blog\app\Models\Blog;
use Modules\Blog\app\Models\BlogComment;
use Throwable;

class BlogController extends Controller
{
    private SeoBlogLibrary $seoBlogLibrary;

    public function __construct(SeoBlogLibrary $seoBlogLibrary)
    {
        $this->seoBlogLibrary = $seoBlogLibrary;
    }

    function index() {
        $allBlogs = $this->allBlogs();
        $filteredBlogs = $this->seoBlogLibrary->filter($allBlogs, request('search'), request('category'));
        $blogs = $this->seoBlogLibrary->paginate($filteredBlogs, 9);
        $categories = $this->seoBlogLibrary->categories($allBlogs);
        $popularBlogs = $this->seoBlogLibrary->popular($allBlogs, 8);

        return view('frontend.pages.blog', compact('blogs', 'categories', 'popularBlogs'));
    }

    function show(string $slug) {
       $allBlogs = $this->allBlogs();
       $blog = $allBlogs->firstWhere('slug', $slug);
       abort_if(!$blog, 404);

       $latestBlogs = $this->seoBlogLibrary->latest($allBlogs, $blog->slug, 8);
       $categories = $this->seoBlogLibrary->categories($allBlogs);
       $comments = data_get($blog, 'is_static')
            ? collect()
            : BlogComment::where(['blog_id' => $blog->id])->where('status', 1)->orderBy('created_at', 'desc')->get();

       return view('frontend.pages.blog-details', compact('blog', 'latestBlogs', 'categories', 'comments'));
    }

    function submitComment(Request $request) {
       $request->validate([
        'comment' => ['required', 'max:1000'], 
        'g-recaptcha-response' => Cache::get('setting')->recaptcha_status == 'active' ? ['required', new CustomRecaptcha()] : 'nullable',
       ], [
        'comment.required' => __('The comment field is required'),
        'comment.max' => __('The comment must not be greater than 1000 characters'),
        'g-recaptcha-response.required' => __('The reCAPTCHA verification is required'),
        'g-recaptcha-response.recaptcha' => __('The reCAPTCHA verification failed'),
       ]);
       $comment = new BlogComment();

       $comment->blog_id = $request->blog_id;
       $comment->user_id = userAuth()->id;
       $comment->comment = $request->comment;
       $comment->save();
       return redirect()->back()->withFragment('comments')->with(['messege' => __('Comment added successfully. waiting for approval'), 'alert-type' => 'success']);
    }

    private function allBlogs()
    {
        try {
            $databaseBlogs = Blog::with(['translation', 'category.translation', 'author'])
                ->whereHas('category', function ($query) {
                    $query->where('status', 1);
                })
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Throwable) {
            $databaseBlogs = collect();
        }

        return $this->seoBlogLibrary->mergeWithDatabase($databaseBlogs);
    }
}
