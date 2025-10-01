@props(['id' => null, 'maxWidth' => null])

<x-jet-dialog-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    @if(isset($title))
        <x-slot name="title">
            {{ $title }}
        </x-slot>
    @endif

    @if(isset($content))
        <x-slot name="content">
            {{ $content }}
        </x-slot>
    @endif

    @if(isset($footer))
        <x-slot name="footer">
            {{ $footer }}
        </x-slot>
    @else
        <x-slot name="footer">
            <x-jet-secondary-button @click="show = false">
                {{ __('Close') }}
            </x-jet-secondary-button>

            @if(isset($action))
                <x-jet-button class="ml-2" @click="{{ $action }}">
                    {{ $button }}
                </x-jet-button>
            @endif
        </x-slot>
    @endif
</x-jet-dialog-modal>
