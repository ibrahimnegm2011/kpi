@php use App\Enums\Menu;use Illuminate\Support\Facades\Auth; @endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{config('app.name')}}</title>
    <meta name="author" content="Sirc">
    <meta name="description" content="">

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body class="bg-[#deebec] font-family-karla flex m-0 max-h-screen">

<aside class="bg-primary-500 w-64 hidden shadow-xl sm:flex sm:flex-col sm:justify-items-start">
    <div class="py-4 px-6">
        <a href="{{route('home')}}"
           class="text-white font-semibold flex items-center">
            <img src="{{url('images/logo-white.png')}}" class="mr-4 h-8 inline"/>
            <div class="w-64">{{config('app.name')}}</div>
        </a>
    </div>
    <nav class="text-white text-base font-semibold h-full overflow-auto no-scrollbar">
        @foreach(Menu::items() as $item)
            @php ! $item instanceof Menu && dd($item)@endphp
            @if(!$item->isGroup() && Auth::user()->hasPermission($item->value))
                <a href="{{route($item->route())}}"
                   class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item {{str_starts_with(request()->route()->getName(), explode('.', $item->route())[0]) ? 'bg-primary-600' : ''}}">
                    {!! $item->icon() !!}
                    {{$item->title()}}
                </a>
            @elseif($item->isGroup())
                <button class="w-full flex justify-between items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 pr-2 nav-item"  aria-controls="dropdown-{{$item->value}}" data-collapse-toggle="dropdown-{{$item->value}}">
                    <span>{{$item->title()}}</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <ul id="dropdown-{{$item->value}}" class="hidden py-2 space-y-2 ml-4">
                    @foreach($item->groupItems() as $subItem)
                        <li>
                            <a href="{{route($subItem->route())}}"
                               class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item {{str_starts_with(request()->route()->getName(), $subItem->route()) ? 'active-nav-link' : ''}}">
                                {!! $subItem->icon() !!}
                                {{$subItem->title()}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </nav>
</aside>

<div class=" w-full h-screen flex flex-col  overflow-y-hidden">
    <!-- Desktop Header -->
    <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
        <div class="w-1/2"></div>
        <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end">
            <button @click="isOpen = !isOpen"
                    class="realtive z-10 w-32 h-12 overflow-hidden hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                {{auth()->user()->name}}
            </button>
            <form x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-10" method="POST"
                  action="{{route('logout')}}">
                @csrf
                <button type="submit" class="block px-4 py-2 w-full account-link hover:text-white">Log Out</button>
            </form>
        </div>
    </header>

    <!-- Mobile Header & Nav -->
    <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
        <div class="flex items-center justify-between">
            <a href="{{route('home')}}"
               class="text-white text-xl font-semibold uppercase flex items-center">
                <img src="{{url('assets/images/logo-white.png')}}" class="mr-1 h-8 inline"/>
                {{config('app.name')}}
            </a>
            <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                <i x-show="!isOpen" class="fas fa-bars"></i>
                <i x-show="isOpen" class="fas fa-times"></i>
            </button>
        </div>

        <!-- Dropdown Nav -->
        <nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">
            @foreach(Menu::items() as $menu)
                @if(!$menu->isGroup() && Auth::user()->hasPermission($menu->value))
                    <a href="{{route($menu->route())}}"
                       class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item {{str_starts_with(request()->route()->getName(), $menu->route()) ? 'active-nav-link' : ''}}">
                        {!! $menu->icon() !!}
                        {{$menu->title()}}
                    </a>
                @endif
            @endforeach
        </nav>
    </header>


    <div class="w-full h-full border-t flex flex-col justify-between overflow-auto">
        <main class="w-full flex-grow p-6">
            <div class="w-full p-5 rounded-3xl bg-white shadow-lg">
                {{ $slot }}
            </div>

            <div class="fixed bottom-0 right-5 max-w-xs w-full">
                @if(session('success'))
                    <div id="toast-success"
                         class="mb-3 flex items-center justify-between p-4 space-x-4 text-gray-100 bg-green-800 divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow space-x"
                         role="alert">
                        <div class="text-sm font-normal">{{session('success')}}</div>
                        <button type="button"
                                class="me-auto -mx-1.5 -my-1.5 border-none bg-green-800 items-center justify-center flex-shrink-0 text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                                data-dismiss-target="#toast-success" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <i class="w-3 h-3 fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div id="toast-error"
                         class="mb-3 flex items-center justify-between max-w-xs p-4 space-x-4 text-gray-100 bg-red-800 divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow dark:text-gray-400 dark:divide-gray-700 space-x dark:bg-gray-800"
                         role="alert">
                        <div class="text-sm font-normal">{{session('error')}}</div>
                        <button type="button"
                                class="me-auto -mx-1.5 -my-1.5 border-none bg-red-800 items-center justify-center flex-shrink-0 text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                                data-dismiss-target="#toast-error" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <i class="w-3 h-3 fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
        </main>

        <footer class="w-full bg-white text-right p-4">
            © {{now()->year}} <img src="{{url('images/footer_icon.png')}}" class="h-3 inline -mt-0.5">. All Rights Reserved.
        </footer>
    </div>

</div>

<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/712f3a091f.js" crossorigin="anonymous"></script>

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
<script
    src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js"
    integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM="
    crossorigin="anonymous"></script>

{{$scripts ?? ""}}

@stack('scripts')

</body>
</html>
