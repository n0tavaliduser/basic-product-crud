<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-100 text-zinc-800 font-sans antialiased text-sm">
    <div class="flex h-screen overflow-hidden p-5 gap-5">
        <!-- Sidebar Floating -->
        {{-- @include('layouts.sidebar') --}}

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-full overflow-hidden gap-5">
            <!-- Topbar Floating -->
            {{-- @include('layouts.topbar') --}}

            <!-- Main Content Canvas -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto rounded-md">
                <div class="max-w-5xl mx-auto pb-10">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>
