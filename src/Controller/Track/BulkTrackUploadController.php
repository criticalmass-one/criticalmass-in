<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * The standalone bulk track upload has been folded into the unified upload, which
 * handles tracks and photos through one form. This route is kept so existing links
 * and bookmarks keep working — it now redirects to the unified upload page.
 */
class BulkTrackUploadController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/bulk', name: 'caldera_criticalmass_track_bulkupload', methods: ['GET'], priority: 310)]
    public function pageAction(): Response
    {
        return $this->redirectToRoute('caldera_criticalmass_unified_upload');
    }
}
