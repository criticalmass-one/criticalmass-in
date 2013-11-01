<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Caldera\CriticalmassCoreBundle\Entity\Comment;
use Caldera\CriticalmassCoreBundle\Entity\CommentImage;

class CommentController extends Controller implements Trackable
{
	public function listcommentsAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $citySlug->getCity()->getId()), array('date' => 'DESC'));

		if ($ride)
		{
			$comments = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Comment')->findBy(array('ride' => $ride->getId()), array('creationDateTime' => 'DESC'));

			$form = $this->createFormBuilder(new Comment())->add('text', 'text')->add('image', 'file')->getForm();

			return $this->render('CalderaCriticalmassMobileBundle:RideComments:list.html.twig', array('comments' => $comments, 'form' => $form->createView()));
		}
		else
		{
			return $this->render('CalderaCriticalmassMobileBundle:RideComments:nocomments.html.twig');
		}
	}

	public function addcommentAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $citySlug->getCity()->getId()), array('date' => 'desc'));

		$comment = new Comment();
		$form = $this->createFormBuilder($comment)->add('text', 'text')->add('image', 'file')->getForm();

		$form->handleRequest($this->getRequest());

		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();

			if ($comment->getImage())
			{
				$commentImage = new CommentImage();

				$commentImage = $this->get('caldera_criticalmass_imageuploader')->setCommentImage($commentImage)->setImageFile($comment->getImage())->processUpload()->getCommentImage();
				$commentImage = $this->get('caldera_criticalmass_imageresizer')->setCommentImage($commentImage)->resize()->save()->getCommentImage();

				$em->persist($commentImage);
				$comment->setImage2($commentImage);
			}

			$comment->setUser($this->getUser());
			$comment->setRide($ride);
			$comment->setCreationDateTime(new \DateTime());

			$em->persist($comment);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('caldera_criticalmass_listcomments', array('citySlug' => $this->getUser()->getCurrentCity()->getMainSlug()->getSlug())));
	}
}
