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

    public function build(?string $text, string $term): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text) ?? '');
        $term = trim($term);

        if ('' === $text) {
            return '';
        }

        if ('' === $term) {
            return htmlspecialchars($this->shorten($text, 0), ENT_QUOTES, 'UTF-8');
        }

        $position = mb_stripos($text, $term);
        $excerpt = $this->shorten($text, false === $position ? 0 : (int) $position);

        return $this->highlight(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'), $term);
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
     * Markiert die Fundstellen im bereits maskierten Text. Der Suchbegriff wird ebenso
     * maskiert, damit er auf die maskierte Fassung passt.
     */
    private function highlight(string $escapedText, string $term): string
    {
        $escapedTerm = htmlspecialchars($term, ENT_QUOTES, 'UTF-8');
        $pattern = '/' . preg_quote($escapedTerm, '/') . '/iu';

        return preg_replace($pattern, '<mark>$0</mark>', $escapedText) ?? $escapedText;
    }
}
