@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'actions' => [],
    'actionsPosition' => HeaderActionsPosition::Adaptive,
    'description' => null,
    'heading' => null,
    'icon' => null,
])

<div
    {{
        $attributes->class([
            'fi-ta-header flex flex-col gap-3 p-4 sm:px-6',
            'sm:flex-row sm:items-center' => $actionsPosition === HeaderActionsPosition::Adaptive,
        ])
    }}
>
    @if ($icon || $heading || $description)
        <div class="flex items-center gap-3">
            @if ($icon)
                <x-filament::icon
                    :icon="$icon"
                    class="fi-ta-header-icon h-6 w-6 self-start text-primary-500 dark:text-primary-400"
                />
            @endif

            <div class="grid gap-y-1">
                @if ($heading)
                    <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $heading }}
                    </h3>
                @endif

                @if ($description)
                    <p class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    @if ($actions)
        <x-filament-tables::actions
            :actions="$actions"
            :alignment="Alignment::Start"
            wrap
            @class([
                'ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive &&
                    ! ($icon || $heading || $description),
                'sm:ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive,
            ])
        />
    @endif
</div>