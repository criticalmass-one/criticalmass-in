<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Deletion;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;

class UserDeleter
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly FilesystemOperator $photoFilesystem,
        private readonly FilesystemOperator $trackFilesystem,
        private readonly FilesystemOperator $userPhotoFilesystem,
    ) {
    }

    public function delete(User $user): void
    {
        $connection = $this->managerRegistry->getConnection();

        if (!$connection instanceof Connection) {
            throw new \RuntimeException('Expected DBAL Connection');
        }

        $userId = $user->getId();

        // Dateinamen vor dem Löschen sammeln
        $photoFiles = $this->getPhotoFiles($connection, $userId);
        $trackFiles = $this->getTrackFiles($connection, $userId);
        $userPhotoFile = $user->getImageName();

        $connection->beginTransaction();

        try {
            // 1. Löschen: Persönliche Daten ohne öffentlichen Wert
            $this->deletePersonalData($connection, $userId);

            // 2. Anonymisieren: Öffentliche Daten mit Wert für die Community
            $this->anonymizePublicData($connection, $userId);

            // 3. User löschen
            $connection->executeStatement('DELETE FROM user WHERE id = ?', [$userId]);

            $connection->commit();

            // 4. Dateien löschen (nach erfolgreichem DB-Commit)
            $this->deleteFiles($photoFiles, $trackFiles, $userPhotoFile);
        } catch (\Throwable $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * @return list<string>
     */
    private function getPhotoFiles(Connection $connection, int $userId): array
    {
        return $connection->fetchFirstColumn(
            'SELECT image_name FROM photo WHERE user_id = ? AND image_name IS NOT NULL',
            [$userId]
        );
    }

    /**
     * @return list<string>
     */
    private function getTrackFiles(Connection $connection, int $userId): array
    {
        return $connection->fetchFirstColumn(
            'SELECT track_filename FROM track WHERE user_id = ? AND track_filename IS NOT NULL',
            [$userId]
        );
    }

    private function deletePersonalData(Connection $connection, int $userId): void
    {
        // Photos
        $connection->executeStatement('DELETE FROM photo WHERE user_id = ?', [$userId]);

        // Tracks
        $connection->executeStatement('DELETE FROM track WHERE user_id = ?', [$userId]);

        // Participations
        $connection->executeStatement('DELETE FROM participation WHERE user_id = ?', [$userId]);

        // TrackImportCandidates
        $connection->executeStatement('DELETE FROM track_import_candidate WHERE user_id = ?', [$userId]);

        // RideEstimates
        $connection->executeStatement('DELETE FROM ride_estimate WHERE user_id = ?', [$userId]);

        // View-Tabellen
        $connection->executeStatement('DELETE FROM thread_view WHERE user_id = ?', [$userId]);
        $connection->executeStatement('DELETE FROM ride_view WHERE user_id = ?', [$userId]);
        $connection->executeStatement('DELETE FROM city_view WHERE user_id = ?', [$userId]);
        $connection->executeStatement('DELETE FROM photo_view WHERE user_id = ?', [$userId]);
        $connection->executeStatement('DELETE FROM promotion_view WHERE user_id = ?', [$userId]);
    }

    private function anonymizePublicData(Connection $connection, int $userId): void
    {
        // Posts (Forum-Beiträge behalten, aber anonymisieren)
        $connection->executeStatement('UPDATE post SET user_id = NULL WHERE user_id = ?', [$userId]);

        // Cities (erstellte Städte behalten)
        $connection->executeStatement('UPDATE city SET user_id = NULL WHERE user_id = ?', [$userId]);

        // Rides (erstellte Touren behalten)
        $connection->executeStatement('UPDATE ride SET user_id = NULL WHERE user_id = ?', [$userId]);

        // CityCycles
        $connection->executeStatement('UPDATE city_cycle SET user_id = NULL WHERE user_id = ?', [$userId]);

        // Subrides
        $connection->executeStatement('UPDATE subride SET user_id = NULL WHERE user_id = ?', [$userId]);

        // FrontpageTeasers
        $connection->executeStatement('UPDATE frontpage_teaser SET user_id = NULL WHERE user_id = ?', [$userId]);

        // SocialNetworkProfiles
        $connection->executeStatement('UPDATE social_network_profile SET user_id = NULL WHERE user_id = ?', [$userId]);
    }

    /**
     * @param list<string> $photoFiles
     * @param list<string> $trackFiles
     */
    private function deleteFiles(array $photoFiles, array $trackFiles, ?string $userPhotoFile): void
    {
        foreach ($photoFiles as $filename) {
            $this->safeDeleteFile($this->photoFilesystem, $filename);
        }

        foreach ($trackFiles as $filename) {
            $this->safeDeleteFile($this->trackFilesystem, $filename);
        }

        if ($userPhotoFile) {
            $this->safeDeleteFile($this->userPhotoFilesystem, $userPhotoFile);
        }
    }

    private function safeDeleteFile(FilesystemOperator $filesystem, string $filename): void
    {
        try {
            if ($filesystem->fileExists($filename)) {
                $filesystem->delete($filename);
            }
        } catch (\Throwable) {
            // Datei-Löschung fehlgeschlagen, aber DB ist bereits committed
            // Logging wäre hier sinnvoll
        }
    }
}
