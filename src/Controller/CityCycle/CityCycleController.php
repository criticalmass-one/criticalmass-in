<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Criticalmass\RideGenerator\ExecuteGenerator\CycleExecutable;
use App\Criticalmass\RideGenerator\ExecuteGenerator\DateTimeListGenerator;
use App\Criticalmass\RideGenerator\RideGenerator\RideGeneratorInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Form\Type\CityCycleType;
use App\Form\Type\ExecuteCityCycleType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CityCycleController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function listAction(City $city): Response
    {
        return $this->render('CityCycle/list.html.twig', [
            'cycles' => $this->getCityCycleRepository()->findByCity($city),
            'city' => $city,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function addAction(Request $request, UserInterface $user = null, City $city, ObjectRouterInterface $objectRouter): Response
    {
        $cityCycle = new CityCycle();
        $cityCycle
            ->setCity($city)
            ->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $objectRouter->generate($city, 'caldera_criticalmass_citycycle_add'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $cityCycle, $form, $objectRouter);
        } else {
            return $this->addGetAction($request, $cityCycle, $form, $objectRouter);
        }
    }

    protected function addGetAction(Request $request, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('CityCycle/edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function addPostAction(Request $request, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($city, 'caldera_criticalmass_citycycle_list'));
        }

        return $this->render('CityCycle/edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function editAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, ObjectRouterInterface $objectRouter): Response
    {
        $cityCycle->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $objectRouter->generate($cityCycle, 'caldera_criticalmass_citycycle_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $cityCycle, $form, $objectRouter);
        } else {
            return $this->editGetAction($request, $user, $cityCycle, $form, $objectRouter);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('CityCycle/edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function editPostAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $form = $this->createForm(CityCycleType::class, $cityCycle, [
                'action' => $objectRouter->generate($cityCycle, 'caldera_criticalmass_citycycle_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('CityCycle/edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function disableAction(CityCycle $cityCycle, ObjectManager $objectManager, ObjectRouterInterface $objectRouter): Response
    {
        if ($cityCycle->getRides()->count() > 0) {
            if (!$cityCycle->getValidFrom()) {
                $cityCycle->setValidFrom($cityCycle->getCreatedAt());
            }

            if (!$cityCycle->getValidUntil()) {
                $cityCycle->setValidUntil(new \DateTime());
            }

            $cityCycle->setDisabledAt(new \DateTime());
        } elseif (0 === $cityCycle->getRides()->count()) {
            $objectManager->remove($cityCycle);
        }

        $objectManager->flush();

        return $this->redirect($objectRouter->generate($cityCycle->getCity(), 'caldera_criticalmass_citycycle_list'));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function executeAction(Request $request, CityCycle $cityCycle, RideGeneratorInterface $generator): Response
    {
        $dateTime = new \DateTime();
        $threeMonthInterval = new \DateInterval('P6M');

        $executeable = new CycleExecutable();
        $executeable
            ->setFromDate(DateTimeUtil::getMonthStartDateTime($dateTime))
            ->setUntilDate(DateTimeUtil::getMonthEndDateTime($dateTime->add($threeMonthInterval)));

        $form = $this->createForm(ExecuteCityCycleType::class, $executeable);
        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateTimeList = DateTimeListGenerator::generateDateTimeList($executeable);

            $generator->addCity($cityCycle->getCity())
                ->setDateTimeList($dateTimeList)
                ->execute();

            $rideList = $generator->getRideList();

            return $this->render('CityCycle/execute_preview.html.twig', [
                'cityCycle' => $cityCycle,
                'executeable' => $executeable,
                'dateTimeList' => $dateTimeList,
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
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function executePersistAction(Request $request, CityCycle $cityCycle, RideGeneratorInterface $generator, SessionInterface $session, RegistryInterface $registry): Response
    {
        if (Request::METHOD_POST === $request->getMethod() && $request->request->getInt('fromDate') && $request->request->get('untilDate')) {
            $executeable = new CycleExecutable();
            $executeable
                ->setFromDate(new \DateTime(sprintf('@%d', $request->request->getInt('fromDate'))))
                ->setUntilDate(new \DateTime(sprintf('@%d', $request->request->getInt('untilDate'))));

            $dateTimeList = DateTimeListGenerator::generateDateTimeList($executeable);

            $generator->addCity($cityCycle->getCity())
                ->setDateTimeList($dateTimeList)
                ->execute();

            $rideList = $generator->getRideList();

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
