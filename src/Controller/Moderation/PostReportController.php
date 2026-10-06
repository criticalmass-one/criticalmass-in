<?php declare(strict_types=1);

namespace App\Controller\Moderation;

use App\Controller\AbstractController;
use App\Criticalmass\FriendlyCaptcha\FriendlyCaptchaInterface;
use App\Criticalmass\Moderation\ModerationMailer;
use App\Criticalmass\Router\PostUrlGenerator;
use App\Entity\Post;
use App\Entity\PostReport;
use App\Entity\User;
use App\Form\Type\PostReportType;
use App\Repository\PostReportRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Beitraege melden.
 *
 * Melden kann jeder, auch ohne Konto (Art. 16 DSA). Gaeste hinterlassen eine
 * Adresse und loesen ein Captcha.
 */
class PostReportController extends AbstractController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly PostReportRepository $reportRepository,
        private readonly PostUrlGenerator $postUrlGenerator,
        private readonly ModerationMailer $mailer,
        private readonly FriendlyCaptchaInterface $friendlyCaptcha,
        private readonly RateLimiterFactory $postReportLimiter
    ) {
        parent::__construct($managerRegistry);
    }

    #[Route('/post/{postId}/report', requirements: ['postId' => '\d+'], name: 'caldera_criticalmass_post_report', priority: 120)]
    public function reportAction(
        Request $request,
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post
    ): Response {
        if (!$post->getEnabled()) {
            throw $this->createNotFoundException('Dieser Beitrag ist nicht mehr sichtbar.');
        }

        $postUrl = $this->postUrlGenerator->generate($post);

        /** @var User|null $user */
        $user = $this->getUser();

        if (null !== $user && $user === $post->getUser()) {
            $this->addFlash('info', 'Eigene Beiträge kannst du nicht melden, aber bearbeiten oder zurückziehen.');

            return $this->redirect($postUrl);
        }

        if (null !== $user && $this->reportRepository->hasOpenReportBy($post, $user)) {
            $this->addFlash('info', 'Du hast diesen Beitrag schon gemeldet. Wir melden uns, sobald wir entschieden haben.');

            return $this->redirect($postUrl);
        }

        $report = (new PostReport($post))->setReporter($user);
        $form = $this->createForm(PostReportType::class, $report, ['guest' => null === $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $user && !$this->friendlyCaptcha->checkCaptcha($request)) {
                $this->addFlash('danger', 'Die Prüfung, ob du ein Mensch bist, hat nicht geklappt. Bitte versuche es noch einmal.');

                return $this->render('Moderation/report.html.twig', ['post' => $post, 'postUrl' => $postUrl, 'form' => $form]);
            }

            $limiterKey = null !== $user ? 'user-' . $user->getId() : 'ip-' . $request->getClientIp();

            if (!$this->postReportLimiter->create($limiterKey)->consume()->isAccepted()) {
                $this->addFlash('danger', 'Du hast in der letzten Stunde schon viele Beiträge gemeldet. Bitte versuche es später noch einmal.');

                return $this->redirect($postUrl);
            }

            $em = $this->managerRegistry->getManager();
            $em->persist($report);
            $em->flush();

            $this->mailer->reportReceived($report);

            $this->addFlash('success', 'Danke für deine Meldung. Ein Mensch aus unserem Team schaut sich den Beitrag an.');

            return $this->redirect($postUrl);
        }

        return $this->render('Moderation/report.html.twig', [
            'post' => $post,
            'postUrl' => $postUrl,
            'form' => $form,
        ]);
    }
}
