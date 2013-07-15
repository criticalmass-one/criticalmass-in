<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Caldera\CriticalmassBundle\Entity\Ride;
use Caldera\CriticalmassBundle\Form\RideType;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Ride controller.
 *
 */
class RideController extends Controller
{

    /**
     * Lists all Ride entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CalderaCriticalmassBundle:Ride')->findAll();

        return $this->render('CalderaCriticalmassBundle:Ride:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Ride entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Ride();
        $form = $this->createForm(new RideType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity->getLocation())
            {
              $entity->setLocation("");
            }

            if (!$entity->getMap())
            {
              $entity->setMap("");
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_ride_show', array('id' => $entity->getId())));
        }

        return $this->render('CalderaCriticalmassBundle:Ride:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Ride entity.
     *
     */
    public function newAction()
    {
        $entity = new Ride();
        $form   = $this->createForm(new RideType(), $entity);

        return $this->render('CalderaCriticalmassBundle:Ride:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ride entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CalderaCriticalmassBundle:Ride:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Ride entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $editForm = $this->createForm(new RideType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CalderaCriticalmassBundle:Ride:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Ride entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RideType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_ride_edit', array('id' => $id)));
        }

        return $this->render('CalderaCriticalmassBundle:Ride:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Ride entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CalderaCriticalmassBundle:Ride')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ride entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_ride'));
    }

    /**
     * Creates a form to delete a Ride entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

		public function notificationsAction($rideId)
		{
			$ride = $this->getDoctrine()->getManager()->getRepository('CalderaCriticalmassBundle:Ride')->find($rideId);

			return $this->render('CalderaCriticalmassBundle:Ride:notifications.html.twig', array('ride' => $ride));
		}

		public function sendnotificationsAction($rideId, $notificationType)
		{
			$ride = $this->getDoctrine()->getManager()->getRepository('CalderaCriticalmassBundle:Ride')->find($rideId);

			switch ($notificationType)
			{
				case 'location':
					$notification = new Utility\Notifications\LocationPublishedNotification($ride);
					break;
			}

			return $this->render('CalderaCriticalmassBundle:Ride:sendnotifications.html.twig', array('ride' => $ride));
		}
}
