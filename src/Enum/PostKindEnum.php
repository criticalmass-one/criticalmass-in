<?php declare(strict_types=1);

namespace App\Enum;

/**
 * Welche Art Beitrag ein Post ist.
 *
 * Der Kontext allein sagt das nicht: Ein Statusbeitrag kann an einer Stadt
 * haengen, genau wie frueher ein Kommentar zur Stadt. Deshalb steht die Art
 * ausdruecklich in der Spalte `post.kind`.
 *
 * Die Datenbank kennt die Werte zusaetzlich ueber einen CHECK
 * (`post_kind_check`). Ein neuer Fall braucht deshalb eine Migration, die
 * diese Bedingung erweitert.
 */
enum PostKindEnum: string
{
    /** Antwort auf eine Tour, ein Foto, ein Forumsthema oder eine Stadt. */
    case COMMENT = 'COMMENT';

    /** Kurzer, freier Beitrag, wie „Wer faehrt am Freitag mit?“. */
    case STATUS = 'STATUS';

    /** Langer Beitrag mit Titel, etwa aus dem frueheren Blog. */
    case ARTICLE = 'ARTICLE';

    public function label(): string
    {
        return match($this) {
            self::COMMENT => 'Kommentar',
            self::STATUS => 'Statusbeitrag',
            self::ARTICLE => 'Artikel',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function choices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}
