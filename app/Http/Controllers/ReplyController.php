<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Reply;

class ReplyController extends Controller
{
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

    public function update(Request $request, Blog $blog, Reply $reply)
    {
        $request->validate([
            'reply' => 'required|string|max:255',
        ]);

        if ($reply->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $reply->update([
            'reply' => $request->input('reply'),
        ]);

        return redirect()->route('blogs.index', ['blog' => $blog]);
    }

    public function destroy(Blog $blog, Reply $reply)
    {
        if ($reply->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $reply->delete();

        return redirect()->route('blogs.index');
    }
}
