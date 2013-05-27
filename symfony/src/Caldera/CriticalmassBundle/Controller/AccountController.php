<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Acme\AccountBundle\Form\Type\RegistrationType;
use Acme\AccountBundle\Form\Model\Registration;

class AccountController extends Controller
{
	public function registerAction()
	{
		$registration = new Registration();
		$form = $this->createForm(new RegistrationType(), $registration, array(
			'action' => $this->generateUrl('account_create'),
		));

		return $this->render(
		'CalderaCriticalmassBundle:Account:register.html.twig',
			array('form' => $form->createView())
		);
	}
}