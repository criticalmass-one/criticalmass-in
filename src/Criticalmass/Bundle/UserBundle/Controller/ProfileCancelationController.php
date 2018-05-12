<?php declare(strict_types=1);

namespace Criticalmass\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Criticalmass\Bundle\UserBundle\Form\Type\UserEmailType;
use Criticalmass\Bundle\UserBundle\Form\Type\UsernameType;

class ProfileCancelationController extends Controller
{
    /**
     * @Security("is_granted('cancel', user)")
     */
    public function cancelAction(Request $request, UserInterface $user): Response
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->cancelGetAction($request, $user);
        }
    }

    protected function cancelGetAction(Request $request, UserInterface $user): Response
    {
        return $this->render('UserBundle:ProfileCancelation:cancel.html.twig');
    }
}
