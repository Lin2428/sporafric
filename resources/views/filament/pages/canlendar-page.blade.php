@php
    use App\Enum\InterventionStatus;
    use App\Enum\InterventionType;
@endphp

<x-filament-panels::page>
    <style>
        /* ── Cartes d'intervention (components/calendar-event) ── */
        .ec .ec-event.gmao-ev { background: transparent !important; box-shadow: none; padding: 0; margin-bottom: 3px; }
        .ec .ec-event.gmao-ev .ec-event-body { padding: 0; }
        .gmao-card {
            --c: #94a3b8;
            background: color-mix(in srgb, var(--c) 10%, #fff);
            border: 1px solid color-mix(in srgb, var(--c) 28%, #fff);
            border-left: 3px solid var(--c);
            border-radius: 6px;
            padding: 3px 6px 4px;
            line-height: 1.25;
            overflow: hidden;
            transition: box-shadow .15s, transform .15s;
        }
        .gmao-card:hover { box-shadow: 0 2px 8px rgba(15, 23, 42, .12); transform: translateY(-1px); }
        .gmao-card__top { display: flex; align-items: center; gap: 4px; }
        .gmao-card__type { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; color: color-mix(in srgb, var(--c) 75%, #000); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .gmao-card__time { font-size: 10px; color: #64748b; font-variant-numeric: tabular-nums; }
        .gmao-card__customer { font-size: 12px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .gmao-card__detail { font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Statut : pastille ; terminée = carte atténuée */
        .gmao-dot { margin-left: auto; flex-shrink: 0; width: 8px; height: 8px; border-radius: 9999px; background: #3b82f6; box-shadow: 0 0 0 2px #fff; }
        .gmao-dot--1 { background: #f59e0b; }
        .gmao-dot--2 { background: #16a34a; }
        .ec-event.gmao-st-2 .gmao-card { opacity: .6; }
        .ec-event.gmao-st-2 .gmao-card:hover { opacity: 1; }

        /* ── Grille ── */
        .ec { --ec-today-bg-color: rgba(255, 204, 0, .08); }
        .ec .ec-day-head { font-size: 12px; font-weight: 600; text-transform: capitalize; color: #64748b; padding: 6px 0; }
        .ec .ec-toolbar { margin-bottom: 14px; }
        .ec .ec-title { font-size: 18px; font-weight: 700; text-transform: capitalize; }
        .ec .ec-button { border-radius: 8px; font-size: 13px; padding: 5px 12px; }
        .ec .ec-button.ec-active { background: rgb(var(--primary-500)); border-color: rgb(var(--primary-500)); color: #1c1c1e; }
        .ec .ec-day-grid .ec-day { min-height: 110px; }
        .ec .ec-day.ec-today .ec-day-head time, .ec .ec-day.ec-today > .ec-day-head { color: #1c1c1e; }

        /* ── Légende ── */
        .gmao-legend { display: flex; flex-wrap: wrap; gap: 8px 16px; align-items: center; font-size: 12px; color: #475569; }
        .gmao-legend__chip { display: inline-flex; align-items: center; gap: 6px; }
        .gmao-legend__swatch { width: 12px; height: 12px; border-radius: 3px; border-left: 3px solid var(--c); background: color-mix(in srgb, var(--c) 18%, #fff); }
        .gmao-legend__sep { width: 1px; height: 16px; background: #e2e8f0; }

        /* ── Sombre ── */
        .dark .gmao-card { background: color-mix(in srgb, var(--c) 16%, #18181b); border-color: color-mix(in srgb, var(--c) 35%, #18181b); }
        .dark .gmao-card__type { color: color-mix(in srgb, var(--c) 70%, #fff); }
        .dark .gmao-card__customer { color: #f4f4f5; }
        .dark .gmao-card__detail, .dark .gmao-card__time, .dark .gmao-legend { color: #a1a1aa; }
        .dark .gmao-dot { box-shadow: 0 0 0 2px #18181b; }
        .dark .gmao-legend__sep { background: #3f3f46; }
    </style>

    {{ $this->form }}

    <div class="gmao-legend">
        @foreach (InterventionType::cases() as $type)
            <span class="gmao-legend__chip"><span class="gmao-legend__swatch" style="--c: {{ $type->color() }}"></span>{{ $type->label() }}</span>
        @endforeach
        <span class="gmao-legend__sep"></span>
        <span class="gmao-legend__chip"><span class="gmao-dot" style="margin: 0;"></span>{{ ucfirst(InterventionStatus::PLANIFIEE->label()) }}</span>
        <span class="gmao-legend__chip"><span class="gmao-dot gmao-dot--1" style="margin: 0;"></span>{{ InterventionStatus::EN_COURS->label() }}</span>
        <span class="gmao-legend__chip"><span class="gmao-dot gmao-dot--2" style="margin: 0;"></span>{{ InterventionStatus::TERMINEE->label() }}</span>
    </div>
</x-filament-panels::page>
