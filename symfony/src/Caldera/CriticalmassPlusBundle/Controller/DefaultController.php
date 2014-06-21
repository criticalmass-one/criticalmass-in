<?php

namespace Caldera\CriticalmassPlusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassPlusBundle\Utility\VoucherCodeGenerator;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($voucherClassId, $number)
    {
        $voucherClass =  $this->getDoctrine()->getRepository('CalderaCriticalmassPlusBundle:VoucherClass')->findOneById($voucherClassId);
        $vcg = new VoucherCodeGenerator($voucherClass, $this->getDoctrine());

        $vcg->execute($number);

        return new Response();
    }

    public function fooAction($action)
    {
        $group =  $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:Group')->findOneById(1);
        $user = $this->getUser();

        if ($action == 'subscribe')
        {
            $user->addGroup($group);
        }
        elseif ($action == 'unsubscribe')
        {
            $user->removeGroup($group);
        }

        $this->get('fos_user.user_manager')->updateUser($user);
        return new Response();
    }
}
