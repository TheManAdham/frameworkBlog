<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Reply;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('user', 'replies')->orderBy('published_at', 'desc')->get();
        
        return view('blogs', compact('blogs'));
    }
    

    public function store(Request $request)
{
    $request->validate([
        'body' => 'required|string',
        'title' => 'required|string',
    ]);

    $blog = new Blog();
    $blog->body = $request->input('body');
    $blog->title = $request->input('title');
    $blog->published_at = now();
    $blog->user_id = Auth::id();
    $blog->save();

    return redirect()->back()->with('success', 'Blog post created successfully!');
}

public function storeReply(Request $request, Blog $blog)
{
    $request->validate([
        'reply' => 'required|string|max:255',
    ]);

    $reply = new Reply([
        'reply' => $request->input('reply'),
        'blog_id' => $blog->id,
        'user_id' => auth()->id(),
    ]);

    $reply->save();

    return redirect()->back()->with('success', 'Reply added successfully!');
}

    public function update(Request $request, Blog $blog)
    {
    $request->validate([
        'body' => 'required|string',
        'title' => 'required|string',
    ]);

    $blog->update([
        'body' => $request->input('body'),
        'title' => $request->input('title'),
    ]);

    return redirect()->back()->with('success', 'Blog post updated successfully!');
    }

     public function destroy(Blog $blog)
    {
        if ($blog->user_id === Auth::id()) {
            $blog->delete();
            return redirect()->back()->with('success', 'Blog post deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'You are not authorized to delete this blog post.');
        }
    }
}