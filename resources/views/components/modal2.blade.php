@props(['name', 'title'])
<div x-data="{ show: false, name: '{{ $name }}' }" x-show="show" x-on:open-modal.window="show = ($event.detail.name === name)"
    x-on:close-modal.window="show = false" x-on:keydown.escape.window="show = false" class="modal"
    :class="{ 'modal-open': show }">
    <div class="modal-box w-11/12 max-w-3xl">
        <form method="dialog">
            <button x-on:click="$dispatch('close-modal')"
                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        @if (isset($title))
            <h3 class="font-bold text-lg">{{ $title }}</h3>
        @endif

        <div class="mt-4 max-h-[calc(100vh-200px)] overflow-y-auto">
            {{ $body }}
        </div>
    </div>

    <div x-show="show" x-on:click="$dispatch('close-modal')" class="modal-backdrop"></div>
</div>
