<?php declare(strict_types=1);

namespace App\Controller\Upload;

use App\Controller\AbstractController;
use App\Criticalmass\Upload\UploadDispatcherInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Unified upload: a single page where users drop GPX/FIT tracks and image files
 * together. Each file is posted individually (one request per file) and routed by the
 * UploadDispatcher to the track or photo pipeline, then matched or parked for review.
 */
class UnifiedUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/upload', name: 'caldera_criticalmass_unified_upload', methods: ['GET'], priority: 310)]
    public function pageAction(): Response
    {
        return $this->render('Upload/unified-upload.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/upload/file', name: 'caldera_criticalmass_unified_upload_file', methods: ['POST'], priority: 310)]
    public function uploadFileAction(
        Request $request,
        UploadDispatcherInterface $uploadDispatcher,
        RateLimiterFactory $uploadLimiter,
        #[CurrentUser] ?User $user = null,
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('unified_upload', (string) $request->request->get('_token'))) {
            return $this->statusResponse('error', 'Ungültiges Sicherheits-Token — bitte lade die Seite neu.', Response::HTTP_FORBIDDEN);
        }

        if (!$user instanceof User) {
            return $this->statusResponse('error', 'Bitte melde dich an, um Dateien hochzuladen.', Response::HTTP_FORBIDDEN);
        }

        if (false === $uploadLimiter->create('user-' . $user->getId())->consume()->isAccepted()) {
            return $this->statusResponse('error', 'Zu viele Uploads in kurzer Zeit. Bitte warte einen Moment.', Response::HTTP_TOO_MANY_REQUESTS);
        }

        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile instanceof UploadedFile) {
            return $this->statusResponse('error', 'Es wurde keine Datei übertragen.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $uploadDispatcher->dispatch(
                $uploadedFile->getPathname(),
                $uploadedFile->getClientOriginalName(),
                $user,
            );
        } catch (\RuntimeException $exception) {
            return $this->statusResponse('error', $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse([
            'status' => $result->status,
            'kind' => $result->kind,
            'message' => $result->message,
        ]);
    }

    private function statusResponse(string $status, string $message, int $httpStatus = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['status' => $status, 'message' => $message], $httpStatus);
    }
}
