<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\User;
use App\Entity\WebauthnCredential;

/**
 * Über die eigenen Passkeys entscheidet ausschließlich, wem sie gehören.
 *
 * Anders als TrackVoter und PhotoVoter gibt es hier bewusst **keine** Ausnahme für
 * ROLE_ADMIN: Wer fremde Passkeys anlegen oder löschen könnte, könnte sich Zugang zu
 * fremden Konten verschaffen oder Nutzer aus ihren aussperren. Administration hat an
 * Anmeldedaten nichts zu suchen.
 */
class WebauthnCredentialVoter extends AbstractVoter
{
    protected function canEdit(WebauthnCredential $credential, User $user): bool
    {
        return $credential->getUser() === $user;
    }

    protected function canDelete(WebauthnCredential $credential, User $user): bool
    {
        return $this->canEdit($credential, $user);
    }
}
