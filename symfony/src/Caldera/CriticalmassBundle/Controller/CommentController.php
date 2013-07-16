<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassBundle\Entity\Comment;
use Caldera\CriticalmassBundle\Entity\CommentImage;

class CommentController extends Controller
{
	public function listcommentsAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city' => $citySlug->getCity()->getId()), array('date' => 'DESC'));

		if ($ride)
		{
			$comments = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Comment')->findBy(array('ride' => $ride->getId()), array('creationDateTime' => 'DESC'));

			$form = $this->createFormBuilder(new Comment())->add('text', 'text')->add('image', 'file')->getForm();

			return $this->render('CalderaCriticalmassBundle:RideComments:list.html.twig', array('comments' => $comments, 'form' => $form->createView()));
		}
		else
		{
			return $this->render('CalderaCriticalmassBundle:RideComments:nocomments.html.twig');
		}
	}

	public function addcommentAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city' => $citySlug->getCity()->getId()), array('date' => 'desc'));

		$comment = new Comment();
		$form = $this->createFormBuilder($comment)->add('text', 'text')->add('image', 'file')->getForm();

		$form->handleRequest($this->getRequest());

		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();

			$commentImage = new CommentImage();
			$commentImage->setUser($this->getUser());
			$commentImage->setCreationDateTime(new \DateTime());
			$commentImage->setFile($comment->getImage());

			$em->persist($commentImage);

			$comment->setUser($this->getUser());
			$comment->setRide($ride);
			$comment->setCreationDateTime(new \DateTime());
			$comment->setImage2($commentImage);

			$em->persist($comment);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('caldera_criticalmass_listcomments', array('citySlug' => 'hamburg')));
	}

	public function viewcommentimageAction($commentId)
	{
		$response = new Response();

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Comment')->findOneById($commentId);

		$response->headers->set('Content-Type', 'image/jpeg');

		$response->setContent(file_get_contents($comment->getImage()->getPath()));

		return $response;
	}
}
