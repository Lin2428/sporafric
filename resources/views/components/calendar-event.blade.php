{{-- Carte d'intervention du planning. Variables Alpine : event (vkurko/calendar). Styles : filament/pages/canlendar-page --}}
<div class="gmao-card" :style="`--c: ${event.extendedProps.color}`"
     :title="`${event.title} — ${event.extendedProps.status_label}\n${event.extendedProps.customer ?? ''}\n${event.extendedProps.detail ?? ''}\n${event.extendedProps.numero ?? ''}`">
    <div class="gmao-card__top">
        <span class="gmao-card__type" x-text="event.title"></span>
        <span class="gmao-card__time" x-show="event.extendedProps.time" x-text="event.extendedProps.time"></span>
        <span class="gmao-dot" :class="`gmao-dot--${event.extendedProps.status}`" :title="event.extendedProps.status_label"></span>
    </div>
    <div class="gmao-card__customer" x-text="event.extendedProps.customer ?? '—'"></div>
    <div class="gmao-card__detail" x-show="event.extendedProps.detail" x-text="event.extendedProps.detail"></div>
</div>
