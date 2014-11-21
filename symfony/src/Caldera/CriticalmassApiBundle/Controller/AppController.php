<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Caldera\CriticalmassApiBundle\Entity\App;
use Caldera\CriticalmassApiBundle\Type\AppType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

class AppController extends Controller
{
    public function listAction()
    {
        $apps = $this->getDoctrine()->getRepository('CalderaCriticalmassApiBundle:App')->findBy(array('user' => $this->getUser()->getId()));

        return $this->render('CalderaCriticalmassApiBundle:App:list.html.twig', array('apps' => $apps));
    }

    public function addAction(Request $request)
    {
        $app = new App();

        $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_add')));

        $form->handleRequest($request);

        $hasErrors = false;

        if ($form->isValid())
        {
            $app->setUser($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($app);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_edit', array('appId' => $app->getId()))));
        }
        elseif ($form->isSubmitted())
        {
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassApiBundle:App:edit.html.twig', array('form' => $form->createView(), 'app2' => null, 'hasErrors' => $hasErrors));
    }

    public function editAction(Request $request, $appId)
    {
        $app = $this->getDoctrine()->getRepository('CalderaCriticalmassApiBundle:App')->find($appId);

        $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_edit', array('appId' => $app->getId()))));

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($app);
            $em->flush();
        }

        return $this->render('CalderaCriticalmassApiBundle:App:edit.html.twig', array('form' => $form->createView(), 'app2' => $app));
    }
}
