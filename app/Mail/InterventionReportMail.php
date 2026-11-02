<?php

namespace App\Mail;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/** Compte rendu d'intervention envoyé au client après signature sur l'app mobile. */
class InterventionReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Intervention $intervention) {}

    public function envelope(): Envelope
    {
        $type = InterventionType::tryFrom((string) $this->intervention->type)?->label() ?? 'Intervention';

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Sporafric Motors — Service technique'),
            subject: "Compte rendu d'intervention {$this->intervention->numero} — {$type}",
        );
    }

    public function content(): Content
    {
        $i = $this->intervention;
        $date = fn ($v) => $v ? Carbon::parse($v)->format('d/m/Y à H:i') : null;

        return new Content(view: 'emails.intervention-report', with: [
            'title'     => 'Compte rendu d\'intervention',
            'reference' => $i->numero,
            'rows'      => array_filter([
                'Type'            => InterventionType::tryFrom((string) $i->type)?->label(),
                'Statut'          => ucfirst(InterventionStatus::tryFrom((string) $i->status)?->label() ?? ''),
                'Client'          => $i->devis?->customer?->name ?? $i->contract?->customer?->name ?? $i->customer?->name,
                'Site'            => $i->site,
                'Groupe'          => trim(($i->generator?->name ?? $i->generator_name) . ($i->generator?->reference ? " ({$i->generator->reference})" : '')),
                'Début'           => $date($i->start_date ?? $i->date_planifiee),
                'Fin'             => $date($i->end_date),
                'Compteur'        => is_numeric($i->houres) ? number_format((float) $i->houres, 0, ',', ' ') . ' h' : null,
                'Technicien(s)'   => $i->interventionTechniciens->pluck('name')->implode(', ') ?: null,
            ]),
            'sections'  => array_filter([
                'Constat'           => $this->plain($i->description_panne),
                'Travaux effectués' => $this->plain($i->travaux),
            ]),
            'model'     => $i,
        ]);
    }

    /** Champs RichEditor → texte simple (sauts de ligne conservés). */
    private function plain(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }
        $text = preg_replace('#<(br|/p|/li|/div)\s*/?>#i', "\n", $html);

        return trim(html_entity_decode(strip_tags($text))) ?: null;
    }
}
