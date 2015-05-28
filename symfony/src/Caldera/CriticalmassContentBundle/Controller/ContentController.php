<?php

namespace Caldera\CriticalmassContentBundle\Controller;

use Caldera\CriticalmassContentBundle\Type\ContentType;
use Michelf\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    public function showAction(Request $request, $slug)
    {
        $content = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:Content')->findBySlug($slug);
        
        $content = array_pop($content);

        if (!$content)
        {
            throw new NotFoundHttpException('Schade, unter dem Stichwort '.$slug.' wurde kein Inhalt hinterlegt.');
        }
        
        $markdown = new Markdown();
        $content->setFormattedText($markdown->transform($content->getText()));
        
        return $this->render('CalderaCriticalmassContentBundle:Content:show.html.twig', array('content' => $content));
    }
    
    public function editAction(Request $request, $slug)
    {
        if (!$this->getUser())
        {
            throw new NotFoundHttpException('Dieser Inhalt darf nur von angemeldeten Teilnehmern editiert werden.');
        }
        
        $content = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:Content')->findBySlug($slug);

        $content = array_pop($content);

        if (!$content)
        {
            throw new NotFoundHttpException('Schade, unter dem Stichwort '.$slug.' wurde kein Inhalt hinterlegt.');
        }

        if (!$content->getIsPublicEditable())
        {
            throw new NotFoundHttpException('Nein, nein, du darfst diesen Inhalt nicht ändern.');
        }
        
        $archiveContent = clone $content;
        $archiveContent->setArchiveUser($this->getUser());
        $archiveContent->setArchiveParent($content);

        $form = $this->createForm(new ContentType(), $content, array('action' => $this->generateUrl('caldera_criticalmass_content_edit', array('slug' => $content->getSlug()))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
            $content->setLastEditionDateTime(new \DateTime());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->persist($archiveContent);
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassContentBundle:Content:edit.html.twig', array('content' => $content, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }
}
