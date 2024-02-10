@vite('resources/css/app.css')
<header class="bg-gray-800 text-white py-4 fixed top-0 w-full z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="" class="text-xl font-bold">Blog.</a>
        <nav>
            <ul class="flex space-x-4">
                @if (Route::has('login'))
                    @auth
                        <li><a href="{{ url('/dashboard') }}" class="hover:text-gray-300">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-gray-300">Log in</a></li>

                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="hover:text-gray-300">Register</a></li>
                        @endif
                    @endauth
                @endif 
            </ul>
        </nav>
    </div>
</header>
