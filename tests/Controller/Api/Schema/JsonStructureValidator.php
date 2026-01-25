<?php declare(strict_types=1);

namespace Tests\Controller\Api\Schema;

use PHPUnit\Framework\Assert;

/**
 * Utility class for validating JSON response structures against PHP schemas.
 *
 * Schema format:
 * - Simple type: 'int', 'string', 'float', 'bool', 'array'
 * - Nullable type: 'int|null', 'string|null', etc.
 * - Nested object: ['field' => 'type', ...]
 * - Array of objects: 'array' (only checks type is array)
 * - Optional field: 'field?' => 'type' (key ends with ?)
 */
final class JsonStructureValidator
{
    private const VALID_TYPES = ['int', 'integer', 'string', 'float', 'double', 'bool', 'boolean', 'array', 'null'];

    /**
     * Validates JSON against a PHP schema.
     *
     * @param array $schema The expected schema definition
     * @param array $actual The actual JSON data to validate
     * @param string $path Current path for error messages (internal use)
     * @throws \PHPUnit\Framework\AssertionFailedError on validation failure
     */
    public static function assertMatchesSchema(array $schema, array $actual, string $path = ''): void
    {
        foreach ($schema as $key => $expectedType) {
            $isOptional = false;
            $actualKey = $key;

            // Check for optional field marker (?)
            if (str_ends_with($key, '?')) {
                $isOptional = true;
                $actualKey = substr($key, 0, -1);
            }

            $fieldPath = $path === '' ? $actualKey : "{$path}.{$actualKey}";

            // Check if key exists
            if (!array_key_exists($actualKey, $actual)) {
                if ($isOptional) {
                    continue; // Optional field not present, that's OK
                }
                Assert::fail("Missing required field '{$fieldPath}' in response. Available keys: " . implode(', ', array_keys($actual)));
            }

            $actualValue = $actual[$actualKey];

            // Handle nested object schema
            if (is_array($expectedType)) {
                if ($actualValue === null) {
                    Assert::fail("Field '{$fieldPath}' expected to be an object but got null");
                }
                if (!is_array($actualValue)) {
                    Assert::fail("Field '{$fieldPath}' expected to be an object but got " . gettype($actualValue));
                }
                self::assertMatchesSchema($expectedType, $actualValue, $fieldPath);
                continue;
            }

            // Parse type string (e.g., 'int|null', 'string')
            self::assertValueMatchesType($expectedType, $actualValue, $fieldPath);
        }
    }

    /**
     * Validates that a value matches the expected type specification.
     */
    private static function assertValueMatchesType(string $typeSpec, mixed $value, string $fieldPath): void
    {
        $types = array_map('trim', explode('|', $typeSpec));
        $actualType = self::getPhpType($value);

        foreach ($types as $type) {
            // Handle special array types like 'array<CitySlug>'
            if (str_starts_with($type, 'array<')) {
                if (is_array($value)) {
                    return; // Just check it's an array, detailed item validation is separate
                }
            }

            if (self::matchesType($type, $actualType, $value)) {
                return;
            }
        }

        $valuePreview = is_scalar($value) ? var_export($value, true) : gettype($value);
        Assert::fail(
            "Field '{$fieldPath}' has type '{$actualType}' (value: {$valuePreview}), expected '{$typeSpec}'"
        );
    }

    /**
     * Check if a value matches a specific type.
     */
    private static function matchesType(string $expectedType, string $actualType, mixed $value): bool
    {
        return match ($expectedType) {
            'int', 'integer' => $actualType === 'integer',
            'string' => $actualType === 'string',
            'float', 'double' => $actualType === 'double' || $actualType === 'integer', // integers are valid floats
            'bool', 'boolean' => $actualType === 'boolean',
            'array' => $actualType === 'array',
            'null' => $value === null,
            default => false,
        };
    }

    /**
     * Get the PHP type of a value.
     */
    private static function getPhpType(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }
        return gettype($value);
    }

    /**
     * Validates that all expected keys are present in the actual data (without type checking).
     *
     * @param array $expectedKeys List of keys that must be present
     * @param array $actual The actual JSON data
     * @throws \PHPUnit\Framework\AssertionFailedError on validation failure
     */
    public static function assertHasKeys(array $expectedKeys, array $actual): void
    {
        $missingKeys = [];

        foreach ($expectedKeys as $key) {
            if (!array_key_exists($key, $actual)) {
                $missingKeys[] = $key;
            }
        }

        if (count($missingKeys) > 0) {
            Assert::fail(
                sprintf(
                    "Missing keys: %s. Available keys: %s",
                    implode(', ', $missingKeys),
                    implode(', ', array_keys($actual))
                )
            );
        }
    }

    /**
     * Validates that the array contains only the expected keys (no extra keys).
     *
     * @param array $expectedKeys List of keys that must be present (and no others)
     * @param array $actual The actual JSON data
     * @throws \PHPUnit\Framework\AssertionFailedError on validation failure
     */
    public static function assertHasExactKeys(array $expectedKeys, array $actual): void
    {
        $actualKeys = array_keys($actual);
        $missingKeys = array_diff($expectedKeys, $actualKeys);
        $extraKeys = array_diff($actualKeys, $expectedKeys);

        $errors = [];
        if (count($missingKeys) > 0) {
            $errors[] = "Missing keys: " . implode(', ', $missingKeys);
        }
        if (count($extraKeys) > 0) {
            $errors[] = "Extra keys: " . implode(', ', $extraKeys);
        }

        if (count($errors) > 0) {
            Assert::fail(implode(". ", $errors));
        }
    }

    /**
     * Extracts the structure from a JSON array (for debugging/schema discovery).
     *
     * @param array $json The JSON data to analyze
     * @return array The inferred structure with type information
     */
    public static function extractStructure(array $json): array
    {
        $structure = [];

        foreach ($json as $key => $value) {
            if (is_array($value)) {
                if (self::isAssociativeArray($value)) {
                    // Nested object
                    $structure[$key] = self::extractStructure($value);
                } else {
                    // List array
                    if (count($value) > 0 && is_array($value[0])) {
                        $structure[$key] = 'array<' . self::describeArrayItem($value[0]) . '>';
                    } else {
                        $structure[$key] = 'array';
                    }
                }
            } else {
                $type = self::getPhpType($value);
                // Convert PHP type names to schema type names
                $type = match ($type) {
                    'integer' => 'int',
                    'double' => 'float',
                    'boolean' => 'bool',
                    'null' => 'null',
                    default => $type,
                };
                $structure[$key] = $type;
            }
        }

        return $structure;
    }

    /**
     * Check if an array is associative (has string keys).
     */
    private static function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Describe an array item for structure extraction.
     */
    private static function describeArrayItem(mixed $item): string
    {
        if (is_array($item) && self::isAssociativeArray($item)) {
            // Return first key as hint
            $keys = array_keys($item);
            return 'object:' . ($keys[0] ?? 'unknown');
        }
        return gettype($item);
    }

    /**
     * Validates each item in an array matches the given schema.
     *
     * @param array $itemSchema The schema each array item should match
     * @param array $items The array of items to validate
     * @param string $arrayName Name for error messages
     */
    public static function assertArrayItemsMatchSchema(array $itemSchema, array $items, string $arrayName = 'array'): void
    {
        Assert::assertIsArray($items, "{$arrayName} should be an array");

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                Assert::fail("{$arrayName}[{$index}] should be an array/object, got " . gettype($item));
            }
            self::assertMatchesSchema($itemSchema, $item, "{$arrayName}[{$index}]");
        }
    }

    /**
     * Pretty-prints a schema for debugging.
     */
    public static function printSchema(array $schema, int $indent = 0): string
    {
        $lines = [];
        $prefix = str_repeat('  ', $indent);

        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $lines[] = "{$prefix}{$key}: {";
                $lines[] = self::printSchema($value, $indent + 1);
                $lines[] = "{$prefix}}";
            } else {
                $lines[] = "{$prefix}{$key}: {$value}";
            }
        }

        return implode("\n", $lines);
    }
}
