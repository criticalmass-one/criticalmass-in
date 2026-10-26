<?php declare(strict_types=1);

namespace App\Enum;

enum PostReportStatusEnum: string
{
    /** Wartet auf eine Entscheidung. */
    case OPEN = 'OPEN';

    /** Der Beitrag wurde daraufhin entfernt. */
    case REMOVED = 'REMOVED';

    /** Der Beitrag bleibt stehen. */
    case DISMISSED = 'DISMISSED';

    public function label(): string
    {
        return match($this) {
            self::OPEN => 'offen',
            self::REMOVED => 'Beitrag entfernt',
            self::DISMISSED => 'zurückgewiesen',
        };
    }
}
