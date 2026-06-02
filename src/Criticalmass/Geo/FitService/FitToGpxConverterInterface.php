<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\FitService;

use phpGPX\Models\GpxFile;

interface FitToGpxConverterInterface
{
    /**
     * Parses a Garmin FIT file and converts its GPS track records into an in-memory
     * phpGPX representation (the same model the application already uses for GPX).
     *
     * @throws \RuntimeException if the file cannot be parsed or contains no GPS track points
     */
    public function convertFileToGpxFile(string $fitFilePath): GpxFile;

    /**
     * Convenience wrapper returning the serialised GPX XML for the given FIT file.
     *
     * @throws \RuntimeException if the file cannot be parsed or contains no GPS track points
     */
    public function convertFileToXmlString(string $fitFilePath): string;
}
