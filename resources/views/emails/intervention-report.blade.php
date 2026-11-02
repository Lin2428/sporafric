{{-- Compte rendu client (intervention ou visite technique). Styles en ligne : clients mail. --}}
@php
    $logo = storage_path('app/public/logo_light.png');
    $signature = $model->signature_path ? storage_path('app/public/' . $model->signature_path) : null;
@endphp
<!doctype html>
<html lang="fr">
<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:24px 12px;">
    <tr><td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:10px; overflow:hidden;">
            {{-- En-tête --}}
            <tr><td style="background:#1c1c1e; padding:20px 24px; border-bottom:4px solid #ffcc00;">
                @if (is_file($logo))
                    <img src="{{ $message->embed($logo) }}" alt="Sporafric Motors" height="22" style="display:block; height:22px; margin-bottom:12px;">
                @endif
                <div style="color:#ffffff; font-size:20px; font-weight:bold;">{{ $title }}</div>
                @if ($reference)<div style="color:#ffcc00; font-size:13px; margin-top:4px;">{{ $reference }}</div>@endif
            </td></tr>

            <tr><td style="padding:22px 24px 6px; font-size:14px; line-height:1.5;">
                Bonjour,<br>
                Veuillez trouver ci-dessous le compte rendu de l'intervention réalisée par nos équipes sur votre groupe électrogène.
            </td></tr>

            {{-- Informations --}}
            <tr><td style="padding:10px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; border:1px solid #e5e7eb; border-radius:8px;">
                    @foreach ($rows as $label => $value)
                        <tr>
                            <td style="padding:8px 12px; width:38%; color:#6b7280; border-bottom:1px solid #f1f5f9;">{{ $label }}</td>
                            <td style="padding:8px 12px; font-weight:bold; border-bottom:1px solid #f1f5f9;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </td></tr>

            {{-- Contrôles (visite technique) --}}
            @if (! empty($checklist))
                <tr><td style="padding:10px 24px;">
                    <div style="font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:6px;">Points contrôlés</div>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                        @foreach ($checklist as $item)
                            <tr>
                                <td style="padding:4px 0; width:24px; font-weight:bold; color:{{ $item['ok'] ? '#16a34a' : '#9ca3af' }};">{{ $item['ok'] ? '✔' : '–' }}</td>
                                <td style="padding:4px 0; color:{{ $item['ok'] ? '#1f2937' : '#9ca3af' }};">{{ $item['label'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td></tr>
            @endif

            {{-- Textes --}}
            @foreach ($sections as $label => $text)
                <tr><td style="padding:10px 24px;">
                    <div style="font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:6px;">{{ $label }}</div>
                    <div style="font-size:13px; line-height:1.55; background:#f9fafb; border-left:3px solid #ffcc00; padding:10px 12px; border-radius:4px;">{!! nl2br(e($text)) !!}</div>
                </td></tr>
            @endforeach

            {{-- Signature --}}
            <tr><td style="padding:14px 24px 22px;">
                <div style="font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; margin-bottom:6px;">Validation client</div>
                @if ($signature && is_file($signature))
                    <div style="border:1px solid #e5e7eb; border-radius:8px; padding:10px; display:inline-block;">
                        <img src="{{ $message->embed($signature) }}" alt="Signature" style="display:block; max-width:260px; height:auto;">
                    </div>
                    <div style="font-size:13px; margin-top:6px;">
                        Signé par <strong>{{ $model->signed_by ?: 'le client' }}</strong>
                        @if ($model->signed_at) le {{ $model->signed_at->format('d/m/Y à H:i') }}@endif
                    </div>
                @else
                    <div style="font-size:13px; color:#92400e; background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:8px 12px;">
                        Non signé : {{ $model->signature_refusal ?: 'client absent' }}
                    </div>
                @endif
            </td></tr>

            <tr><td style="background:#f9fafb; padding:16px 24px; font-size:12px; color:#6b7280; line-height:1.5; border-top:1px solid #e5e7eb;">
                Pour toute question, contactez notre service technique : Pointe-Noire 05 208 80 08 · Brazzaville 05 208 80 13.<br>
                Sporafric Motors — Avenue Georges Dumond, Pointe-Noire — www.sporafric.net<br>
                <span style="color:#9ca3af;">Message envoyé automatiquement, merci de ne pas y répondre.</span>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
