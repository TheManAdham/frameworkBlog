<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
    </head>
    
@vite('resources/css/app.css')
    
<body class="antialiased bg-gray-600">

@include('components.header')

<h1 class="text-3xl font-bold mb-8 text-center my-20 text-white">Blogs</h1>
<p class="text-1xl font-bold mb-8 text-center my-20 text-white">Make sure you are logged in to start blogging ;)</p>

@auth 
<form action="{{ route('blogs.store') }}" method="POST" class="mb-8 flex justify-center items-center">
    @csrf
    <div class="flow">
        <textarea name="title" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4" rows="1" placeholder="Title"></textarea>
        <textarea name="body" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mr-4 my-10" rows="5" placeholder="Write your blog post here..."></textarea>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Post</button>
    </div>
</form>
@endauth

<div class="mb-8 flex flex-col justify-center items-center">
    @foreach ($blogs as $blog)
    <div class="bg-white w-1/2 p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mr-4 my-10">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-2">{{ $blog->title }}</h2>
            <p class="text-gray-600 mb-2">{{ $blog->body }}</p>
            <br><br>
            <p class="text-gray-600">Published on: {{ $blog->published_at ? $blog->published_at->format('M d, Y H:i:s') : 'Date and time not available' }}</p>
            @if ($blog->user)
            <p class="text-gray-600">Published by: {{ $blog->user->name }}</p>
            @else
            <p class="text-gray-600">Published by: Unknown User</p>
            @endif

            @if ($blog->updated_at != $blog->created_at)
            <p><em>Last edited on: {{ $blog->updated_at->format('M d, Y H:i:s') }}</em></p>
            @endif
            @auth
            <div class="flex justify-end mt-2">
                <button type="button" onclick="showReplyModal('{{ $blog->id }}')" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Reply</button>
            </div>

            <div id="replyModal_{{ $blog->id }}" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-75 flex justify-center items-center">
                <div class="bg-white p-8 rounded-lg w-96">
                    <form action="{{ route('blogs.reply', $blog) }}" method="POST" id="replyForm_{{ $blog->id }}">
                        @csrf
                        <textarea id="replyBody_{{ $blog->id }}" name="reply" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4" rows="5" placeholder="Write your reply here..."></textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:bg-green-600">Reply</button>
                            <button type="button" onclick="closeReplyModal('{{ $blog->id }}')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:bg-gray-600 ml-4">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            @if (auth()->id() === $blog->user_id)
            <div class="flex justify-end mt-2">
                <button type="button" onclick="showEditModal('{{ $blog->id }}')" class="text-blue-500 hover:underline mr-4">Edit</button>
                <form id="deleteForm_{{ $blog->id }}" action="{{ route('blogs.destroy', $blog) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete('{{ $blog->id }}')" class="text-red-500 hover:underline">Delete</button>
                </form>
            </div>
            @endif
            @endauth
            @if ($blog->replies->count() > 0)
            <button type="button" onclick="toggleReplies('{{ $blog->id }}')" class="text-blue-500 hover:underline mt-4">See Replies ({{ $blog->replies->count() }})</button>
            <div id="replies_{{ $blog->id }}" class="hidden mt-4">
                <ul>
                    @foreach ($blog->replies as $reply)
                    <li class="px-4 py-2 rounded-md border border-gray-600 m-3">
                        {{ $reply->reply }} - {{ $reply->user->name }} <br>
                        @if ($reply->created_at)
                        <br><em>Replied on: {{ $reply->created_at->format('M d, Y H:i:s') }}</em>
                        @endif
                        @if ($reply->updated_at != $reply->created_at)
                        <br><em>Last edited on: {{ $reply->updated_at->format('M d, Y H:i:s') }}</em>
                        @endif
                        @if (auth()->id() === $reply->user_id)
                        <br>
                        <button class="text-blue-500 hover:underline mr-4" onclick="showEditReplyModal('{{ $blog->id }}', '{{ $reply->id }}')">Edit</button>
                        <button type="button" onclick="confirmDeleteReply('{{ $reply->id }}')" class="text-red-500 hover:underline">Delete</button>
                        @endif
                    </li>
                    <div id="editReplyModal_{{ $blog->id }}_{{ $reply->id }}" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-75 flex justify-center items-center">
                        <div class="bg-white p-8 rounded-lg w-96">
                            <h2 class="text-xl font-semibold mb-4">Edit Reply</h2>
                            <form action="{{ route('blogs.replies.update', ['blog' => $blog, 'reply' => $reply]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                    <textarea id="replyBody_{{ $blog->id }}_{{ $reply->id }}" name="reply" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4" rows="5" placeholder="Edit your reply here...">{{ $reply->reply }}</textarea>
                                         <div class="flex justify-end">
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Save</button>
                                            <button type="button" onclick="hideEditReplyModal('{{ $blog->id }}', '{{ $reply->id }}')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:bg-gray-600 ml-4">Cancel</button>
                                        </div>
                            </form>
                        </div>
                </div>
                <div id="deleteReplyModal_{{ $reply->id }}" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-75 flex justify-center items-center">
                    <div class="bg-white p-8 rounded-lg z-50">
                        <p class="text-xl">Are you sure you want to delete this reply?</p>
                            <div class="mt-4 flex justify-end">
                                <form id="deleteReplyForm_{{ $reply->id }}" action="{{ route('blogs.replies.destroy', ['blog' => $blog, 'reply' => $reply]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg mr-4">Delete</button>
                                </form>
                                 <button type="button" onclick="cancelDeleteReply('{{ $reply->id }}')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Cancel</button>
                            </div>
                    </div>
                </div>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    <div id="editModal_{{ $blog->id }}" class="fixed top-0 left-0 flex items-center justify-center w-full h-full bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md">
            <h2 class="text-2xl mb-4">Edit Blog Post</h2>
            <form action="{{ route('blogs.update', $blog) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea name="title" rows="1" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4">{{ $blog->title }}</textarea>
                <textarea name="body" rows="5" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4">{{ $blog->body }}</textarea>
                <div class="flex justify-between">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Save</button>
                    <button type="button" onclick="hideEditModal('{{ $blog->id }}')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div id="deleteConfirmationModal_{{ $blog->id }}" class="hidden fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-75 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg z-50">
            <p class="text-xl">Are you sure you want to delete this blog?</p>
            <div class="mt-4 flex justify-end">
                <form id="deleteBlogForm_{{ $blog->id }}" action="{{ route('blogs.destroy', ['blog' => $blog]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg mr-4">Delete</button>
                </form>
                <button type="button" onclick="cancelDelete('{{ $blog->id }}')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Cancel</button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script src="{{ asset('build/assets/blogs.js') }}"></script>

</body>

</html>