<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\BikerightVoucher;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class BikerightController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function viewAction(UserInterface $user = null): Response
    {
        $bikerightVoucher = $this->getVoucher($user);

        return $this->render('BikeRight/view.html.twig', [
            'bikerightVoucher' => $bikerightVoucher,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function generateAction(UserInterface $user = null): Response
    {
        $bikerightVoucher = $this->getVoucher($user);

        if (!$bikerightVoucher) {
            $this->assignVoucher($user);
        }

        return $this->redirectToRoute('criticalmass_user_bikeright_view');
    }


    protected function assignVoucher(User $user): BikerightVoucher
    {
        $bikerightVoucher = $this->getBikerightVoucherRepository()->findUnassignedVoucher();

        if (!$bikerightVoucher) {
            throw $this->createNotFoundException();
        }

        $bikerightVoucher
            ->setUser($user)
            ->setAssignedAt(new \DateTime());

        $this->getDoctrine()->getManager()->flush();

        return $bikerightVoucher;
    }

    protected function getVoucher(User $user): ?BikerightVoucher
    {
        return $this->getBikerightVoucherRepository()->findOneByUser($user);
    }
}
