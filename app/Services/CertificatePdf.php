<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

// Every certificate PDF in the project renders through here — the web's
// /certificates/{id}/download, the app's Learning certificates, the
// volunteer ID card and the Nikah Counselor ID card. Each of those used to
// carry its own copy of the view name, paper size and filename, so a change
// to (say) the ID-card dimensions had to be found in four files. Type is the
// only thing that decides how a certificate is drawn, so that mapping lives
// in one match() here.
class CertificatePdf
{
    /**
     * 85.6mm x 54mm (ISO/IEC 7810 ID-1) in points, rounded up slightly —
     * rounding down left the page a hair short of the CSS-declared mm size,
     * which made DomPDF spill a near-invisible sliver onto a blank 2nd page.
     */
    private const ID_CARD_PAPER = [0, 0, 242.7, 153.15];

    public static function download(Certificate $certificate): Response
    {
        // A pending/rejected course-certificate request has no
        // certificate_number yet and was never actually issued — the
        // three admin-triggered types (volunteer_id, nikah_counselor_id,
        // admin) are always created already-approved, so this only ever
        // actually bites the course-request flow.
        abort_unless($certificate->isApproved(), 404);

        $certificate->loadMissing('user', 'course', 'issuer');

        $number = $certificate->certificate_number;

        return match (true) {
            $certificate->type === 'volunteer_id' => Pdf::loadView('certificates.volunteer-id-card', compact('certificate'))
                ->setPaper(self::ID_CARD_PAPER)
                ->download("Sallaamti-Volunteer-ID-{$number}.pdf"),

            $certificate->type === 'nikah_counselor_id' => Pdf::loadView('certificates.nikah-counselor-id', compact('certificate'))
                ->setPaper(self::ID_CARD_PAPER)
                ->download("Sallaamti-Nikah-Counselor-ID-{$number}.pdf"),

            // Digital Skills courses get their own branded certificate; every
            // other course falls through to the Quran/general design.
            $certificate->course?->track === 'skills' => Pdf::loadView('certificates.pdf-skills', compact('certificate'))
                ->setPaper('a4', 'landscape')
                ->download("Sallaamti-Certificate-{$number}.pdf"),

            default => Pdf::loadView('certificates.pdf', compact('certificate'))
                ->setPaper('a4', 'landscape')
                ->download("Sallaamti-Certificate-{$number}.pdf"),
        };
    }

    /** Human label for a certificate in a list — the app's My Certificates screen. */
    public static function label(Certificate $certificate): string
    {
        return $certificate->title
            ?? $certificate->course?->title
            ?? match ($certificate->type) {
                'volunteer_id' => 'Volunteer ID Card',
                'nikah_counselor_id' => 'Nikah Counselor ID Card',
                default => 'Certificate',
            };
    }
}
