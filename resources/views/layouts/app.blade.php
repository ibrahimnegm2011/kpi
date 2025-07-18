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
    <div class="py-3 px-6">
        <a href="{{route('home')}}"
           class="text-white font-semibold flex items-center">
            <img src="{{url('images/logo-white.png')}}" class="mx-auto h-10 inline"/>
        </a>
    </div>
    <nav class="text-white text-base font-semibold h-full overflow-auto no-scrollbar">
        @foreach(Menu::items() as $item)
            @if(!$item->isGroup() && Auth::user()->hasPermission($item->value))
                <a href="{{route($item->route())}}"
                   class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item {{$item->isActive() ? 'bg-primary-600' : ''}}">
                    {!! $item->icon() !!}
                    {{$item->title()}}
                </a>
            @endif
        @endforeach
    </nav>
</aside>

<div class=" w-full h-screen flex flex-col  overflow-y-hidden">
    <!-- Desktop Header -->
    <header class="w-full items-center bg-white py-2 px-3 hidden sm:flex">
        <div class="w-1/2 text-xl">{{config('app.name')}}</div>
        <div class="w-1/2 flex justify-end gap-2">
            <div class="min-w-40">
                {{-- For Agent Users Select Account --}}
                @php $accounts = Auth::user()->agentAccounts(); @endphp
                @if(Auth::user()->type == \App\Enums\UserType::AGENT && $accounts->count() > 1)
                    <form method="post" id="account-form" action="{{route('agent.account.change')}}">
                        @csrf
                        <select name="accountId" class="max-w-xs w-full" onchange="this.form.submit()">
                            @foreach($accounts as $account)
                                <option value="{{$account->id}}" {{session('selected_account') !== $account->id ? '' : 'selected' }}>
                                    {{$account->name}}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
            <div x-data="{ isOpen: false }" class="relative">
                <button
                    @click="isOpen = !isOpen"
                    @keydown.escape="isOpen = false"
                    aria-haspopup="true"
                    :aria-expanded="isOpen.toString()"
                    class="relative z-10 flex items-center w-44 min-h-12 px-4 py-2 bg-white hover:bg-primary-50 focus:bg-primary-50 focus:outline-none transition"
                >
                    <span class="flex flex-col items-start flex-1 min-w-0">
                        <span class="font-semibold truncate w-full" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                        @if(Auth::user()->account)
                            <span class="text-xs text-gray-500 truncate w-full" title="{{ Auth::user()->account->name  }}">
                                {{ Auth::user()->account->name }}
                            </span>
                        @endif
                    </span>

                    <i class="fas fa-chevron-down ml-3 text-gray-400"></i>
                </button>

                <div x-show="isOpen" @click.away="isOpen = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="absolute right-0 w-44 rounded-lg shadow-lg bg-white z-20 border border-gray-100"
                    style="display: none;"
                    tabindex="-1">
{{--                    <a href="#" class="flex items-center w-full px-4 py-2 text-left text-gray-700 hover:bg-primary-50 transition">--}}
{{--                        <i class="fas fa-user mr-2"></i> Profile--}}
{{--                    </a>--}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex items-center w-full px-4 py-2 text-left text-gray-700 hover:bg-primary-50 transition"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- Mobile Header & Nav -->
    <header x-data="{ isOpen: false }" class="w-full bg-primary-500 py-5 px-6 sm:hidden max-h-[33vh] overflow-y-auto">
        <div class="flex items-center justify-between">
            <a href="{{route('home')}}"
               class="text-white text-xl font-semibold uppercase flex items-center">
                <img src="{{url('images/logo-white.png')}}" class="mr-1 h-8 inline"/>
                KMS
            </a>
            @if(Auth::user()->type == \App\Enums\UserType::AGENT && $accounts->count() > 1)
                <form method="post" id="account-form" action="{{route('agent.account.change')}}">
                    @csrf
                    <select name="accountId" class="max-w-xs w-full" onchange="this.form.submit()">
                        @foreach($accounts as $account)
                            <option value="{{$account->id}}" {{session('selected_account') !== $account->id ? '' : 'selected' }}>
                                {{$account->name}}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
            <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                <i x-show="!isOpen" class="fas fa-bars"></i>
                <i x-show="isOpen" class="fas fa-times"></i>
            </button>
        </div>

        <!-- Dropdown Nav -->
        <nav x-show="isOpen" class="flex flex-col pt-4">
            @foreach(Menu::items() as $menu)
                @if(!$menu->isGroup() && Auth::user()->hasPermission($menu->value))
                    <a href="{{route($menu->route())}}"
                       class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item {{str_starts_with(request()->route()->getName(), $menu->route()) ? 'active-nav-link' : ''}}">
                        {!! $menu->icon() !!}
                        {{$menu->title()}}
                    </a>
                @endif
            @endforeach

            <div class="flex justify-between gap-3">
                <div class="content-center text-white">
                    <i class="fas fa-user ml-2"></i>
                    {{auth()->user()->name}}
                </div>
                <form x-show="isOpen" method="POST"
                      action="{{route('logout')}}">
                    @csrf
                    <button type="submit" class="px-2 py-1 bg-white rounded-lg shadow-lg hover:bg-secondary-200">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
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
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                         style="display: none;"
                         class="mb-3 flex items-center justify-between p-4 space-x-4 text-gray-100 bg-green-800 divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow space-x"
                         role="alert">
                        <div class="text-sm font-normal">{{session('success')}}</div>
                        <button type="button"
                                class="me-auto -mx-1.5 -my-1.5 border-none bg-green-800 items-center justify-center flex-shrink-0 text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                                @click="show = false" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <i class="w-3 h-3 fas fa-times"></i>
                        </button>
                    </div>
                @endif

                    @if(session('error'))
                        <div id="toast-error"
                             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                             style="display: none;"
                             class="mb-3 flex items-center justify-between max-w-xs p-4 space-x-4 text-gray-100 bg-red-800 divide-x rtl:divide-x-reverse divide-gray-200 rounded-lg shadow dark:text-gray-400 dark:divide-gray-700 space-x dark:bg-gray-800"
                             role="alert">
                            <div class="text-sm font-normal">{{ session('error') }}</div>
                            <button
                                type="button"
                                class="me-auto -mx-1.5 -my-1.5 border-none bg-red-800 items-center justify-center flex-shrink-0 text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                                @click="show = false" aria-label="Close">
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
