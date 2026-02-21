<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Form\Type\ExecuteCityCycleType;
use App\Model\RideGenerator\CycleExecutable;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityCycleExecuteController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private string $rideGeneratorBaseUrl;

    public function __construct(HttpClientInterface $httpClient, string $criticalmassRideGeneratorUrl)
    {
        $this->httpClient = $httpClient;
        $this->rideGeneratorBaseUrl = rtrim($criticalmassRideGeneratorUrl, '/');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/cycles/{id}/execute', name: 'caldera_criticalmass_citycycle_execute', priority: 80)]
    public function executeAction(
        Request $request,
        CityCycle $cityCycle,
        SerializerInterface $serializer
    ): Response {
        $dateTime = new Carbon();
        $sixMonthInterval = new CarbonInterval('P6M');

        $executeable = new CycleExecutable();
        $executeable
            ->setCityCycle($cityCycle)
            ->setFromDate($dateTime->startOfMonth())
            ->setUntilDate((clone $dateTime)->add($sixMonthInterval)->endOfMonth());

        $form = $this->createForm(ExecuteCityCycleType::class, $executeable);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $json = $serializer->serialize($executeable, 'json');

            $response = $this->httpClient->request('POST', $this->rideGeneratorBaseUrl . '/api/preview', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $json,
            ]);

            $rideList = $serializer->deserialize($response->getContent(), Ride::class.'[]', 'json', [
                'groups' => ['api-write'],
            ]);

            return $this->render('CityCycle/execute_preview.html.twig', [
                'cityCycle' => $cityCycle,
                'executeable' => $executeable,
                'dateTimeList' => [],
                'form' => $form->createView(),
                'rideList' => $rideList,
            ]);
        }

        return $this->render('CityCycle/execute_datetime.html.twig', [
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/cycles/{id}/execute-persist', name: 'caldera_criticalmass_citycycle_execute_persist', priority: 80)]
    public function executePersistAction(
        Request $request,
        CityCycle $cityCycle,
        SessionInterface $session,
        ManagerRegistry $registry,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        if (
            $request->isMethod('POST') &&
            $request->request->getInt('fromDate') &&
            $request->request->get('untilDate')
        ) {
            $executeable = new CycleExecutable();
            $executeable
                ->setFromDate((new \DateTime())->setTimestamp($request->request->getInt('fromDate')))
                ->setUntilDate((new \DateTime())->setTimestamp((int) $request->request->get('untilDate')))
                ->setCityCycle($cityCycle);

            $json = $serializer->serialize($executeable, 'json');

            $response = $this->httpClient->request('POST', $this->rideGeneratorBaseUrl . '/api/preview', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $json,
            ]);

            $rideList = $serializer->deserialize($response->getContent(), Ride::class.'[]', 'json', [
                'groups' => ['api-write'],
            ]);

            $em = $registry->getManager();

            /** @var Ride $ride */
            foreach ($rideList as $ride) {
                $ride->setCity($cityCycle->getCity());
                $ride->setCycle($cityCycle);
                $ride->setCreatedAt(new \DateTime());

                $errors = $validator->validate($ride);

                if (count($errors) > 0) {
                    continue;
                }

                $em->persist($ride);
            }

            $em->flush();

            $session->getFlashBag()->add('success', sprintf('Es wurden <strong>%d Touren</strong> automatisch angelegt.', count($rideList)));

            return $this->redirectToRoute('caldera_criticalmass_city_listrides', [
                'citySlug' => $cityCycle->getCity()->getMainSlug()->getSlug(),
                'id' => $cityCycle->getId(),
            ]);
        }

        return $this->redirectToRoute('caldera_criticalmass_citycycle_execute', [
            'citySlug' => $cityCycle->getCity()->getMainSlug()->getSlug(),
            'id' => $cityCycle->getId(),
        ]);
    }
}
