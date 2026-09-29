@props([
    'align' => 'right',
    'id' => null,
])

@php
    $dropdownId = $id ?? 'dropdown-' . uniqid();
@endphp

<div
    class="inline-block text-left"
    x-data="{
        open: false,
        top: 0,
        left: 0,
        calculatePosition() {
            const btn = this.$refs.button;
            if (!btn) return;
            const rect = btn.getBoundingClientRect();
            const menuWidth = 192;
            const menuHeight = 160;
            const spaceBelow = window.innerHeight - rect.bottom;

            if (spaceBelow < menuHeight && rect.top > menuHeight) {
                this.top = rect.top - menuHeight - 4;
            } else {
                this.top = rect.bottom + 4;
            }

            @if ($align === 'left')
                this.left = rect.left;
            @else
                this.left = rect.right - menuWidth;
            @endif

            if (this.left < 8) this.left = 8;
            if (this.left + menuWidth > window.innerWidth - 8) {
                this.left = window.innerWidth - menuWidth - 8;
            }
        },
        toggle() {
            if (!this.open) {
                this.calculatePosition();
                this.open = true;
                this.$nextTick(() => {
                    const btn = this.$refs.button;
                    const menu = this.$refs.menu;
                    if (btn && menu) {
                        const rect = btn.getBoundingClientRect();
                        const menuHeight = menu.offsetHeight;
                        const spaceBelow = window.innerHeight - rect.bottom;
                        if (spaceBelow < menuHeight && rect.top > menuHeight) {
                            this.top = rect.top - menuHeight - 4;
                        } else {
                            this.top = rect.bottom + 4;
                        }
                    }
                });
            } else {
                this.open = false;
            }
        }
    }"
    @click.outside="open = false"
    @close.stop="open = false"
    @scroll.window="if(open) open = false"
    @resize.window="if(open) open = false"
>
    <button
        type="button"
        x-ref="button"
        @click="toggle()"
        class="inline-flex items-center justify-center p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <span class="sr-only">Open options</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
        </svg>
    </button>

    <div
        x-ref="menu"
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        :style="`top: ${top}px; left: ${left}px;`"
        class="fixed z-[9999] w-48 rounded-xl bg-white shadow-2xl ring-1 ring-black/10 border border-slate-200 divide-y divide-slate-100 focus:outline-none py-1 text-left"
        style="display: none;"
    >
        {{ $slot }}
    </div>
</div>


