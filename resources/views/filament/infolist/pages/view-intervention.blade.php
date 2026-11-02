{{--
    Fiche d'intervention (maintenance ET location) — affichée sous l'en-tête Sporafric (components.report-header)
    et imprimée telle quelle (bouton « Imprimer »). Styles préfixés .iv- ; pièces jointes / photos masquées à l'impression.
--}}
@php
    use App\Enum\InterventionStatus;
    use App\Enum\InterventionType;
    use App\Enum\InterventionTypeService;
    use Illuminate\Support\Carbon;

    $r = $getRecord();
    $type    = InterventionType::tryFrom((string) $r->type);
    $status  = InterventionStatus::tryFrom((string) $r->status);
    $service = InterventionTypeService::tryFrom((string) $r->type_service);
    $color   = $type?->color() ?? '#64748b';

    $customer = $r->contract?->customer?->name
        ?? (trim(($r->devis?->customer?->name ?? '') . ' ' . ($r->devis?->customer_name ?? '')) ?: null)
        ?? $r->customer?->name;
    $site  = $r->site ?: ($r->generator?->contractGenerator?->site ?? $r->generator?->devisGenerator?->site);
    $phone = $r->contract?->customer?->contact_c_phone ?? $r->devis?->customer?->contact_c_phone ?? $r->customer?->contact_c_phone;
    $phone = $phone && $phone !== '0' ? $phone : null;
    $siteContact = $r->generator?->contractGenerator?->contact_name ?? $r->generator?->devisGenerator?->contact_name;

    $fmt  = fn ($d) => $d ? Carbon::parse($d)->format('d/m/Y à H:i') : null;
    $start = $r->start_date ? Carbon::parse($r->start_date) : null;
    $end   = $r->end_date ? Carbon::parse($r->end_date) : null;
    $duration = null;
    if ($start && $end && $end->gt($start)) {
        $minutes  = (int) $start->diffInMinutes($end);
        $duration = $minutes >= 1440
            ? intdiv($minutes, 1440) . ' j ' . intdiv($minutes % 1440, 60) . ' h'
            : intdiv($minutes, 60) . ' h ' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);
    }

    $statusStyle = match ($status) {
        InterventionStatus::PLANIFIEE => ['#dbeafe', '#1e40af'],
        InterventionStatus::EN_COURS  => ['#fef3c7', '#92400e'],
        InterventionStatus::TERMINEE  => ['#dcfce7', '#166534'],
        default                       => ['#f1f5f9', '#475569'],
    };
    if ($r->cancelled) {
        $statusStyle = ['#fee2e2', '#991b1b'];
    }

    $num = fn ($v) => is_numeric($v) ? number_format((float) $v, 0, ',', ' ') : null;
    $tav = $r->generator?->next_vidange;
    $tavColor = $tav === null ? '#64748b' : ($tav <= 5 ? '#dc2626' : ($tav <= 30 ? '#d97706' : '#16a34a'));

    $hasHtml = fn ($html) => filled(trim(strip_tags((string) $html)));
    $pieces  = $r->pieces;
    $piecesTotal = $pieces->sum(fn ($p) => (float) $p->pivot->qty * (float) $p->pivot->price);
    $files  = $r->fiches->filter(fn ($f) => $f->fiche);
    $photos = $r->photos()->latest()->get();
    $initials = fn ($name) => collect(preg_split('/\s+/', trim((string) $name)))->filter()->reject(fn ($w) => in_array(strtoupper($w), ['MOT', 'MR', 'M.', 'MME']))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: '?';
@endphp

<style>
    .iv { --iv-c: {{ $color }}; color: #1f2937; font-size: 14px; display: flex; flex-direction: column; gap: 16px; margin-top: 8px; }
    .iv-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px 18px; }
    .iv-title { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #64748b; display: flex; align-items: center; gap: 6px; margin-bottom: 12px; }
    .iv-title svg { width: 16px; height: 16px; color: #94a3b8; }
    .iv-title .iv-count { margin-left: auto; font-weight: 600; color: #94a3b8; letter-spacing: 0; text-transform: none; }

    /* Bandeau */
    .iv-hero { border-left: 5px solid var(--iv-c); display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: center; justify-content: space-between; }
    .iv-client { font-size: 22px; font-weight: 800; line-height: 1.2; }
    .iv-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
    .iv-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .iv-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: #334155; white-space: nowrap; }
    .iv-badge--type { background: color-mix(in srgb, var(--iv-c) 14%, #fff); color: color-mix(in srgb, var(--iv-c) 70%, #000); }
    .iv-badge--type::before { content: ''; width: 8px; height: 8px; border-radius: 9999px; background: var(--iv-c); }
    .iv-hero-dates { display: grid; grid-template-columns: auto auto; gap: 4px 14px; font-size: 13px; }
    .iv-hero-dates dt { color: #64748b; }
    .iv-hero-dates dd { font-weight: 600; text-align: right; }

    /* Grilles */
    .iv-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }
    .iv-grid-2 { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); }
    .iv-dl { display: grid; grid-template-columns: minmax(110px, auto) 1fr; gap: 8px 12px; margin: 0; }
    .iv-dl dt { color: #64748b; font-size: 13px; }
    .iv-dl dd { margin: 0; font-weight: 600; text-align: right; word-break: break-word; }
    .iv-muted { color: #94a3b8; font-weight: 400; font-style: italic; }
    .iv-big { font-size: 22px; font-weight: 800; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .iv-big small { font-size: 12px; font-weight: 600; color: #64748b; }
    .iv-meter { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
    .iv-meter > div { background: #f8fafc; border-radius: 10px; padding: 10px 12px; }
    .iv-meter label { display: block; font-size: 11px; color: #64748b; margin-bottom: 4px; }

    /* Textes */
    .iv-text { border-left: 3px solid var(--iv-c); background: #f8fafc; border-radius: 0 8px 8px 0; padding: 12px 14px; line-height: 1.6; min-height: 52px; }
    .iv-text p { margin: 0 0 6px; } .iv-text ul { list-style: disc; padding-left: 18px; } .iv-text ol { list-style: decimal; padding-left: 18px; }

    /* Techniciens */
    .iv-techs { display: flex; flex-wrap: wrap; gap: 10px; }
    .iv-tech { display: flex; align-items: center; gap: 10px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px 12px 8px 8px; min-width: 220px; }
    .iv-avatar { width: 36px; height: 36px; border-radius: 9999px; background: #ffcc00; color: #1c1c1e; font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .iv-tech-name { font-weight: 700; font-size: 13px; }
    .iv-tech-meta { font-size: 12px; color: #64748b; }

    /* Tableau matériel */
    .iv-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .iv-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; font-weight: 700; padding: 8px 10px; border-bottom: 2px solid #e5e7eb; }
    .iv-table td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; }
    .iv-table .r { text-align: right; font-variant-numeric: tabular-nums; }
    .iv-table tfoot td { font-weight: 800; border-bottom: 0; border-top: 2px solid #e5e7eb; }

    /* Fichiers / photos */
    .iv-files { display: flex; flex-wrap: wrap; gap: 8px; }
    .iv-file { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px 12px; font-size: 13px; color: #1f2937; }
    .iv-file:hover { border-color: var(--iv-c); }
    .iv-file svg { width: 16px; height: 16px; color: #64748b; }
    .iv-photos { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }
    .iv-photos img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 10px; border: 1px solid #e5e7eb; transition: transform .15s; }
    .iv-photos a:hover img { transform: scale(1.02); }

    /* Validation */
    .iv-sign { display: flex; flex-wrap: wrap; gap: 18px; align-items: center; }
    .iv-sign img { width: 240px; max-width: 100%; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; padding: 6px; }
    .iv-pill { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }

    .dark .iv { color: #e4e4e7; }
    .dark .iv-card, .dark .iv-tech, .dark .iv-file { background: #18181b; border-color: #3f3f46; }
    .dark .iv-text, .dark .iv-meter > div { background: #27272a; }
    .dark .iv-badge { background: #27272a; color: #d4d4d8; }
    .dark .iv-badge--type { background: color-mix(in srgb, var(--iv-c) 22%, #18181b); color: color-mix(in srgb, var(--iv-c) 65%, #fff); }
    .dark .iv-table th, .dark .iv-table td, .dark .iv-table tfoot td { border-color: #3f3f46; }

    @media print {
        .fi-header, .iv-noprint { display: none !important; }
        .iv { gap: 10px; font-size: 12px; }
        .iv-card { border-color: #d1d5db; padding: 10px 12px; break-inside: avoid; }
        .iv-grid, .iv-grid-2 { grid-template-columns: 1fr 1fr; gap: 10px; }
        .iv-client { font-size: 18px; }
        @page { margin: 20px 40px 10px 40px; }
        body { margin: 0; }
    }
</style>

<div class="iv">
    {{-- ── Bandeau ── --}}
    <div class="iv-card iv-hero">
        <div>
            <div class="iv-client">{{ $customer ?? 'Client non renseigné' }}</div>
            @if ($site)<div class="iv-sub">{{ $site }}</div>@endif
            <div class="iv-badges">
                <span class="iv-badge iv-badge--type">{{ $type?->label() ?? 'Intervention' }}</span>
                <span class="iv-badge" style="background: {{ $statusStyle[0] }}; color: {{ $statusStyle[1] }};">
                    {{ $r->cancelled ? 'Annulée' : ucfirst($status?->label() ?? '—') }}
                </span>
                @if ($service)<span class="iv-badge">{{ $service->label() }}</span>@endif
                @if ($r->identifiant)<span class="iv-badge">Bon de travaux n° {{ $r->identifiant }}</span>@endif
            </div>
        </div>
        <dl class="iv-hero-dates">
            <dt>Prise d'appel</dt><dd>{{ $fmt($r->date_prise_appel) ?? '—' }}</dd>
            <dt>Planifiée</dt><dd>{{ $fmt($r->date_planifiee) ?? '—' }}</dd>
            @if ($duration)<dt>Durée</dt><dd>{{ $duration }}</dd>@endif
        </dl>
    </div>

    {{-- ── Informations ── --}}
    <div class="iv-grid">
        <div class="iv-card">
            <div class="iv-title">@svg('icon-generator') Groupe électrogène</div>
            <dl class="iv-dl">
                <dt>GE</dt><dd>{{ $r->generator?->name ?? $r->generator_name ?? '—' }}</dd>
                @if ($r->generator?->reference)<dt>Référence</dt><dd>{{ $r->generator->reference }}</dd>@endif
                @if ($r->generator?->power ?? $r->power)<dt>Puissance</dt><dd>{{ $r->generator?->power ?? $r->power }} kVA</dd>@endif
                @if ($r->serial_number)<dt>N° de série</dt><dd>{{ $r->serial_number }}</dd>@endif
                @if ($type === InterventionType::REMPLACEMENT && $r->newGenerator)
                    <dt>Remplacé par</dt>
                    <dd><a href="{{ url('admin/generators/' . $r->newGenerator->id) }}" style="color: #2563eb;">{{ $r->newGenerator->name }}</a></dd>
                @endif
            </dl>
        </div>

        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-map-pin') Site & contact</div>
            <dl class="iv-dl">
                <dt>Site</dt><dd>{!! $site ? e($site) : '<span class="iv-muted">Non renseigné</span>' !!}</dd>
                @if ($siteContact)<dt>Contact</dt><dd>{{ $siteContact }}</dd>@endif
                <dt>Téléphone</dt><dd>{!! $phone ? '<a href="tel:' . e($phone) . '">' . e($phone) . '</a>' : '<span class="iv-muted">Non renseigné</span>' !!}</dd>
                @if ($r->client_email)<dt>Email</dt><dd>{{ $r->client_email }}</dd>@endif
            </dl>
        </div>

        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-clock') Réalisation</div>
            <dl class="iv-dl">
                <dt>Début</dt><dd>{!! $start ? e($start->format('d/m/Y à H:i')) : '<span class="iv-muted">—</span>' !!}</dd>
                <dt>Fin</dt><dd>{!! $end ? e($end->format('d/m/Y à H:i')) : '<span class="iv-muted">—</span>' !!}</dd>
                @if ($duration)<dt>Durée</dt><dd>{{ $duration }}</dd>@endif
                @if ($r->facturable !== null)<dt>Facturable</dt><dd>{{ $r->facturable ? 'Oui' : 'Non' }}</dd>@endif
            </dl>
        </div>
    </div>

    {{-- ── Compteur ── --}}
    <div class="iv-card">
        <div class="iv-title">@svg('heroicon-s-cog') Compteur du groupe</div>
        <div class="iv-meter">
            <div>
                <label>Heures de fonctionnement</label>
                <div class="iv-big">{{ $num($r->houres) ?? $num($r->generator?->houres) ?? '—' }} <small>h</small></div>
            </div>
            <div>
                <label>Vidange programmée à</label>
                <div class="iv-big">{{ $num($r->generator?->prochain_visite) ?? '—' }} <small>h</small></div>
            </div>
            <div>
                <label>Temps restant avant vidange</label>
                <div class="iv-big" style="color: {{ $tavColor }};">{{ $num($tav) ?? '—' }} <small>h</small></div>
            </div>
        </div>
    </div>

    {{-- ── Constat / travaux ── --}}
    <div class="iv-grid-2">
        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-magnifying-glass') Constat</div>
            <div class="iv-text">{!! $hasHtml($r->description_panne) ? $r->description_panne : '<span class="iv-muted">Non renseigné</span>' !!}</div>
        </div>
        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-wrench-screwdriver') Travaux effectués</div>
            <div class="iv-text">{!! $hasHtml($r->travaux) ? $r->travaux : '<span class="iv-muted">Non renseigné</span>' !!}</div>
        </div>
    </div>

    {{-- ── Techniciens ── --}}
    <div class="iv-card">
        <div class="iv-title">@svg('heroicon-s-user-group') Techniciens affectés <span class="iv-count">{{ $r->interventionTechniciens->count() }}</span></div>
        @if ($r->interventionTechniciens->isNotEmpty())
            <div class="iv-techs">
                @foreach ($r->interventionTechniciens as $technicien)
                    <div class="iv-tech">
                        <div class="iv-avatar">{{ $initials($technicien->name) }}</div>
                        <div>
                            <div class="iv-tech-name">{{ $technicien->name }}</div>
                            <div class="iv-tech-meta">{{ collect([$technicien->job, $technicien->phone, $technicien->email])->filter()->implode(' · ') ?: '—' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <span class="iv-muted">Aucun technicien affecté</span>
        @endif
    </div>

    {{-- ── Matériel ── --}}
    <div class="iv-card">
        <div class="iv-title">@svg('heroicon-s-cog-6-tooth') Matériel livré / installé <span class="iv-count">{{ $pieces->count() }}</span></div>
        @if ($pieces->isNotEmpty())
            <table class="iv-table">
                <thead>
                    <tr><th>Désignation</th><th>Référence</th><th class="r">Qté</th><th class="r">Prix unitaire</th><th class="r">Total</th></tr>
                </thead>
                <tbody>
                    {{-- Import Odoo : `reference` contient le libellé, `designation` le code article --}}
                    @foreach ($pieces as $piece)
                        <tr>
                            <td style="font-weight: 600;">{{ $piece->reference }}</td>
                            <td style="color: #64748b;">{{ $piece->designation }}</td>
                            <td class="r">{{ $piece->pivot->qty }}</td>
                            <td class="r">{{ \App\Utils\NumberUtils::format($piece->pivot->price) }} FCFA</td>
                            <td class="r">{{ \App\Utils\NumberUtils::format((float) $piece->pivot->qty * (float) $piece->pivot->price) }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="r">Total</td><td class="r">{{ \App\Utils\NumberUtils::format($piecesTotal) }} FCFA</td></tr>
                </tfoot>
            </table>
        @else
            <span class="iv-muted">Aucune pièce livrée</span>
        @endif
    </div>

    {{-- ── Pièces jointes & photos (écran uniquement) ── --}}
    <div class="iv-grid-2 iv-noprint">
        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-paper-clip') Pièces jointes <span class="iv-count">{{ $files->count() }}</span></div>
            @if ($files->isNotEmpty())
                <div class="iv-files">
                    @foreach ($files as $file)
                        <span class="iv-file">
                            @svg('heroicon-s-document')
                            <a href="{{ url('storage/devis/' . $file->fiche) }}" target="_blank" rel="noopener noreferrer" title="Ouvrir">{{ \Illuminate\Support\Str::limit($file->fiche, 40) }}</a>
                            <a href="{{ route('file.download', ['folder' => 'devis', 'filename' => $file->fiche]) }}" title="Télécharger">@svg('heroicon-s-arrow-down-tray')</a>
                        </span>
                    @endforeach
                </div>
            @else
                <span class="iv-muted">Aucune pièce jointe</span>
            @endif
        </div>

        <div class="iv-card">
            <div class="iv-title">@svg('heroicon-s-camera') Photos <span class="iv-count">{{ $photos->count() }}</span></div>
            @if ($photos->isNotEmpty())
                <div class="iv-photos">
                    @foreach ($photos as $photo)
                        <a href="{{ asset('storage/' . $photo->path) }}" target="_blank" rel="noopener noreferrer" title="{{ $photo->filename }} — {{ $photo->created_at?->format('d/m/Y H:i') }}">
                            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->filename }}">
                        </a>
                    @endforeach
                </div>
            @else
                <span class="iv-muted">Aucune photo</span>
            @endif
        </div>
    </div>

    {{-- ── Validation client (imprimée) ── --}}
    <div class="iv-card">
        <div class="iv-title">@svg('heroicon-s-pencil-square') Validation client</div>
        @if ($r->signature_path || $r->signature_refusal)
            <div class="iv-sign">
                @if ($r->signature_path)
                    <a href="{{ asset('storage/' . $r->signature_path) }}" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('storage/' . $r->signature_path) }}" alt="Signature du client">
                    </a>
                @endif
                <dl class="iv-dl" style="min-width: 260px;">
                    @if ($r->signature_path)
                        <dt>Signé par</dt><dd>{{ $r->signed_by ?: '—' }}</dd>
                    @else
                        <dt>Non signé</dt><dd style="color: #b45309;">{{ $r->signature_refusal }}</dd>
                    @endif
                    @if ($r->signed_at)<dt>Le</dt><dd>{{ $r->signed_at->format('d/m/Y à H:i') }}</dd>@endif
                    <dt>Compte rendu</dt>
                    <dd>
                        @if ($r->report_sent_at)
                            <span class="iv-pill" style="background: #dcfce7; color: #166534;">Envoyé le {{ $r->report_sent_at->format('d/m/Y à H:i') }}</span>
                            <div style="font-weight: 400; font-size: 12px; color: #64748b; margin-top: 2px;">{{ $r->client_email }}</div>
                        @elseif ($r->report_error)
                            <span class="iv-pill" style="background: #fee2e2; color: #991b1b;">Non envoyé — {{ $r->report_error }}</span>
                        @else
                            <span class="iv-pill" style="background: #f1f5f9; color: #475569;">En attente</span>
                        @endif
                    </dd>
                </dl>
            </div>
        @else
            <span class="iv-muted">Pas encore signée par le client</span>
        @endif
    </div>
</div>
