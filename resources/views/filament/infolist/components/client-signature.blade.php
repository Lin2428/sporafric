{{-- Validation client (app mobile) + suivi de l'email de compte rendu. $model : Intervention | TechnicalVisits --}}
<div class="data files">
    <span class="label">
        <i class="icon">@svg('heroicon-s-pencil-square')</i>
        Validation client
    </span>

    @if ($model->signature_path || $model->signature_refusal)
        <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; margin-top: 8px;">
            @if ($model->signature_path)
                <a href="{{ asset('storage/' . $model->signature_path) }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('storage/' . $model->signature_path) }}" alt="Signature"
                         style="width: 220px; height: auto; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff;">
                </a>
            @endif
            <div style="font-size: 13px; line-height: 1.6;">
                @if ($model->signature_path)
                    <div>Signé par <strong>{{ $model->signed_by ?: '—' }}</strong></div>
                @else
                    <div style="color: #b45309;"><strong>Non signé</strong> : {{ $model->signature_refusal }}</div>
                @endif
                @if ($model->signed_at)<div>Le {{ $model->signed_at->format('d/m/Y à H:i') }}</div>@endif
                <div>
                    Compte rendu :
                    @if ($model->report_sent_at)
                        <span style="color: #15803d; font-weight: 600;">envoyé</span> à {{ $model->client_email }} le {{ $model->report_sent_at->format('d/m/Y à H:i') }}
                    @elseif ($model->report_error)
                        <span style="color: #b91c1c; font-weight: 600;">non envoyé</span> ({{ $model->report_error }})
                    @else
                        <span style="color: #6b7280;">en attente</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <span>Pas encore signée</span>
    @endif
</div>
