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
		$form = $this->createForm(new RegistrationType(), new Registration());

		return $this->render(
			'CalderaCriticalmassBundle:Account:register.html.twig',
			array('form' => $form->createView())
		);
	}

	public function processregistrationAction()
	{
		$em = $this->getDoctrine()->getEntityManager();

		$form = $this->createForm(new RegistrationType(), new Registration());

		$form->bind($this->getRequest());

		if ($form->isValid())
		{
			$registration = $form->getData();

			$factory = $this->get('security.encoder_factory');

			$user = $registration->getUser();

			$encoder = $factory->getEncoder($user);
			$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
			$user->setPassword($password);

			$em->persist($user);
			$em->flush();

			return $this->redirect("");
		}


		return $this->render(
			'CalderaCriticalmassBundle:Account:register.html.twig',
			array('form' => $form->createView())
		);
	}
}