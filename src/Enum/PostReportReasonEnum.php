<?php declare(strict_types=1);

namespace App\Enum;

/**
 * Warum jemand einen Beitrag meldet.
 */
enum PostReportReasonEnum: string
{
    case ILLEGAL = 'ILLEGAL';
    case HARASSMENT = 'HARASSMENT';
    case PRIVACY = 'PRIVACY';
    case SPAM = 'SPAM';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match($this) {
            self::ILLEGAL => 'Rechtswidriger Inhalt',
            self::HARASSMENT => 'Beleidigung oder Belästigung',
            self::PRIVACY => 'Persönliche Daten ohne Einwilligung',
            self::SPAM => 'Spam oder Werbung',
            self::OTHER => 'Etwas anderes',
        };
    }

    /**
     * Bei diesen Gruenden laesst sich ohne Erklaerung nicht entscheiden. Fuer
     * rechtswidrige Inhalte verlangt das auch Art. 16 Abs. 2 DSA.
     */
    public function requiresExplanation(): bool
    {
        return self::ILLEGAL === $this || self::OTHER === $this;
    }
}
