<nav aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1 text-xs font-medium text-zinc-500">
        @foreach($items as $label => $url)
            <li>
                <div class="flex items-center">
                    @if(!$loop->first)
                        <svg class="h-3 w-3 text-zinc-400 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif
                    @if($url)
                        <a href="{{ $url }}" class="hover:text-zinc-900 transition-colors">{{ $label }}</a>
                    @else
                        <span class="text-zinc-900">{{ $label }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
