<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\ContentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends AbstractController
{
    public function showAction(Request $request, $slug)
    {
        $content = $this->getContentRepository()->findBySlug($slug);

        if (!$content) {
            throw new NotFoundHttpException('Schade, unter dem Stichwort ' . $slug . ' wurde kein Inhalt hinterlegt.');
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Content:show.html.twig',
            [
                'content' => $content
            ]
        );
    }

    public function editAction(Request $request, $slug)
    {
        $content = $this->getContentRepository()->findBySlug($slug);

        if (!$content) {
            throw new NotFoundHttpException('Schade, unter dem Stichwort ' . $slug . ' wurde kein Inhalt hinterlegt.');
        }

        if (!$content->getIsPublicEditable()) {
            throw new NotFoundHttpException('Nein, nein, du darfst diesen Inhalt nicht ändern.');
        }

        $content->setUser($this->getUser());

        $archiveContent = clone $content;
        $archiveContent->setArchiveParent($content);

        $form = $this->createForm(
            new ContentType(),
            $content,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_content_edit',
                    [
                        'slug' => $content->getSlug()
                    ]
                )
            ]
        );

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;
        $hasSaved = false;

        if ($form->isValid()) {
            $content->setLastEditionDateTime(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->persist($archiveContent);
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
            $hasSaved = true;
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
            $hasSaved = true;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Content:edit.html.twig',
            [
                'content' => $content,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'hasSaved' => $hasSaved
            ]
        );
    }
}
