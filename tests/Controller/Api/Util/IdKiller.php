<?php declare(strict_types=1);

namespace Tests\Controller\Api\Util;

/**
 * @deprecated
 */
class IdKiller
{
    private function __construct()
    {
    }

    /**
     * @deprecated
     */
    public static function removeIds(string $json): string
    {
        $json = json_decode($json, true);

        $json = self::walkRecursiveRemove($json, function ($value, string $key): bool {
            return (stripos($key, 'id') !== false);
        });

        return json_encode($json);
    }

    public static function walkRecursiveRemove(array $array, callable $callback): array {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::walkRecursiveRemove($value, $callback);
            } else {
                if ($callback($value, $key)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
}