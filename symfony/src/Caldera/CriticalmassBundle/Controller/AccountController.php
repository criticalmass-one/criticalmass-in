<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassBundle\Form\Type\RegistrationType;
use Caldera\CriticalmassBundle\Form\Model\Registration;

class AccountController extends Controller
{
	public function registerAction()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$form = $this->createForm(new RegistrationType(), new Registration());

		$form->handleRequest($request);

		if ($form->isValid())
		{
			$registration = $form->getData();

			$em->persist($registration->getUser());
			$em->flush();

			return $this->redirect("/");
		}

		return $this->render(
			'CalderaCriticalmassBundle:Account:register.html.twig',
			array('form' => $form->createView())
    );
	}
}