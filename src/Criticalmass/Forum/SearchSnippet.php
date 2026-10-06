<?php declare(strict_types=1);

namespace App\Criticalmass\Forum;

/**
 * Schneidet aus einem Beitrag den Ausschnitt rund um den Suchbegriff heraus und
 * hebt die Treffer hervor.
 *
 * Der Text wird zuerst maskiert und erst danach mit Markierungen versehen — andersherum
 * käme der Beitragstext als HTML in die Seite.
 */
class SearchSnippet
{
    private const RADIUS = 120;

    /** Steuerzeichen als Platzhalter für die Markierung; im Text werden sie entfernt. */
    private const MARK_OPEN = "\x02";
    private const MARK_CLOSE = "\x03";

    public function build(?string $text, string $term): string
    {
        $text = str_replace([self::MARK_OPEN, self::MARK_CLOSE], '', (string) $text);
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
        $term = trim($term);

        if ('' === $text) {
            return '';
        }

        if ('' === $term) {
            return htmlspecialchars($this->shorten($text, 0), ENT_QUOTES, 'UTF-8');
        }

        $position = mb_stripos($text, $term);
        $excerpt = $this->shorten($text, false === $position ? 0 : (int) $position);

        return $this->highlight($excerpt, $term);
    }

    private function shorten(string $text, int $position): string
    {
        if (mb_strlen($text) <= 2 * self::RADIUS) {
            return $text;
        }

        $start = max(0, $position - self::RADIUS);
        $excerpt = mb_substr($text, $start, 2 * self::RADIUS);

        return ($start > 0 ? '… ' : '') . $excerpt . ' …';
    }

    /**
     * Markiert die Fundstellen und maskiert danach.
     *
     * Andersherum -- erst maskieren, dann markieren -- zerlegt ein Suchbegriff wie
     * „quot“ die Entities, die beim Maskieren entstanden sind: aus &quot; wuerde
     * &<mark>quot</mark>; und damit sichtbarer Zeichensalat. Die Fundstellen werden
     * deshalb zuerst mit zwei Steuerzeichen eingefasst, die im Text nicht vorkommen
     * duerfen, und erst nach dem Maskieren zu echten Markierungen.
     */
    private function highlight(string $excerpt, string $term): string
    {
        $pattern = '/' . preg_quote($term, '/') . '/iu';
        $marked = preg_replace($pattern, self::MARK_OPEN . '$0' . self::MARK_CLOSE, $excerpt);

        if (null === $marked) {
            return htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8');
        }

        return str_replace(
            [self::MARK_OPEN, self::MARK_CLOSE],
            ['<mark>', '</mark>'],
            htmlspecialchars($marked, ENT_QUOTES, 'UTF-8')
        );
    }
}
