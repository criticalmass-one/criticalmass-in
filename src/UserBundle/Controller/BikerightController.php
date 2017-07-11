<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\BikerightVoucher;
use AppBundle\Entity\User;
use AppBundle\Repository\BikerightVoucherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class BikerightController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function viewAction(Request $request, UserInterface $user): Response
    {
        $bikerightVoucher = $this->getVoucher($user);

        return $this->render(
            'UserBundle:BikeRight:view.html.twig',
            [
                'bikerightVoucher' => $bikerightVoucher,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function generateAction(Request $request, UserInterface $user): Response
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
            ->setAssignedAt(new \DateTime())
        ;

        $this->getDoctrine()->getManager()->flush();

        return $bikerightVoucher;
    }

    protected function getVoucher(User $user): ?BikerightVoucher
    {
        return $this->getBikerightVoucherRepository()->findOneByUser($user);
    }

    protected function getBikerightVoucherRepository(): BikerightVoucherRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:BikerightVoucher');
    }

}
