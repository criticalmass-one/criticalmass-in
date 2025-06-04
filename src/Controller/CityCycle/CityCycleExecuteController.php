<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Form\Type\ExecuteCityCycleType;
use App\Model\RideGenerator\CycleExecutable;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CityCycleExecuteController extends AbstractController
{
    protected Client $rideGeneratorClient;

    public function __construct(string $criticalmassRideGeneratorUrl)
    {
        $this->rideGeneratorClient = new Client([
            'verify' => false,
            'base_uri' => $criticalmassRideGeneratorUrl,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function executeAction(Request $request, CityCycle $cityCycle, SerializerInterface $serializer): Response
    {
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
            $result = $this->rideGeneratorClient->post('/api/preview', ['content-type' => 'text/json', 'body' => $serializer->serialize($executeable, 'json'),]);

            $rideList = $serializer->deserialize($result->getBody()->getContents(), 'array<App\Entity\Ride>', 'json');

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

    /**
     * @Security("is_granted('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function executePersistAction(Request $request, CityCycle $cityCycle, SessionInterface $session, ManagerRegistry $registry, SerializerInterface $serializer): Response
    {
        if (Request::METHOD_POST === $request->getMethod() && $request->request->getInt('fromDate') && $request->request->get('untilDate')) {
            $executeable = new CycleExecutable();
            $executeable
                ->setFromDate(new \DateTime(sprintf('@%d', $request->request->getInt('fromDate'))))
                ->setUntilDate(new \DateTime(sprintf('@%d', $request->request->getInt('untilDate'))))
                ->setCityCycle($cityCycle);

            $result = $this->rideGeneratorClient->post('/api/preview', ['content-type' => 'text/json', 'body' => $serializer->serialize($executeable, 'json'),]);

            $rideList = $serializer->deserialize($result->getBody()->getContents(), 'array<App\Entity\Ride>', 'json');

            $em = $registry->getManager();

            /** @var Ride $ride */
            foreach ($rideList as $ride) {
                $em->persist($ride);
            }

            $em->flush();

            $flashMessage = sprintf('Es wurden <strong>%d Touren</strong> automatisch angelegt.', count($rideList));

            $session->getFlashBag()->add('success', $flashMessage);

            return $this->redirectToRoute('caldera_criticalmass_city_listrides', [
                'citySlug' => $cityCycle->getCity()->getMainSlug()->getSlug(),
                'cityCycleId' => $cityCycle->getId(),
            ]);
        }

        return $this->redirectToRoute('caldera_criticalmass_citycycle_execute', [
            'citySlug' => $cityCycle->getCity()->getMainSlug()->getSlug(),
            'cityCycleId' => $cityCycle->getId(),
        ]);
    }
}
