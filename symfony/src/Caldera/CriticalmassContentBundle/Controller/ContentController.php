<?php

namespace Caldera\CriticalmassContentBundle\Controller;

use Caldera\CriticalmassContentBundle\Type\ContentType;
use Michelf\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
    public function showAction(Request $request, $slug)
    {
        $content = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:Content')->findBy(array('slug' => $slug, 'enabled' => true, 'isArchived' => false));
        
        $content = array_pop($content);
        
        $markdown = new Markdown();
        $parsedText = $markdown->transform($content->getText());
        
        return $this->render('CalderaCriticalmassContentBundle:Content:show.html.twig', array('content' => $content, 'parsedText' => $parsedText));
    }
    
    public function editAction(Request $request, $slug)
    {
        $content = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:Content')->findBy(array('slug' => $slug, 'enabled' => true, 'isArchived' => false));

        $content = array_pop($content);
        
        $archiveContent = clone $content;
        $archiveContent->setArchiveUser($this->getUser());
        $archiveContent->setArchiveParent($content);

        $form = $this->createForm(new ContentType(), $content, array('action' => $this->generateUrl('caldera_criticalmass_content_edit', array('slug' => $content->getSlug()))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
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
