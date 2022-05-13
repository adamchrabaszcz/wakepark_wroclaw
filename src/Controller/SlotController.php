<?php

namespace App\Controller;

use App\Calculator\SlotPriceCalculator;
use App\Entity\Option;
use App\Entity\Slot;
use App\Form\SlotType;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/slot")
 */
class SlotController extends AbstractController
{
    /**
     * @Route("/", name="app_slot_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->calendar($entityManager);
    }

    /**
     * @Route("/calendar", name="app_slot_calendar", methods={"GET"})
     */
    public function calendar(EntityManagerInterface $entityManager): Response
    {
        $slot = new Slot();
        $rider = $this->getUser();

        if ($rider) {
            $slot->setRider($rider);
        }

        $form = $this->createForm(SlotType::class, $slot, [
            'action' => $this->generateUrl('app_slot_new')
        ]);

        return $this->render('slot/calendar.html.twig', [
            'form' => $form->createView(),
            'rider' => $rider ?? null
        ]);
    }

    /**
     * @Route("/new", name="app_slot_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SlotPriceCalculator $slotPriceCalculator): Response
    {
        $slot = new Slot();
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slot->setPrice($slotPriceCalculator->calculateSlotPrice($slot));
            $entityManager->persist($slot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_slot_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_slot_show", methods={"GET"})
     */
    public function show(Slot $slot): Response
    {
        return $this->render('slot/show.html.twig', [
            'slot' => $slot,
        ]);
    }

    /**
     * @Route("/{id}", name="app_slot_delete", methods={"POST"})
     */
    public function delete(Request $request, Slot $slot, EntityManagerInterface $entityManager): Response
    {
        if (
            $this->isCsrfTokenValid('delete'.$slot->getId(), $request->request->get('_token'))
            && $this->getUser() == $slot->getRider()
        ) {
            $entityManager->remove($slot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_slot_index', [], Response::HTTP_SEE_OTHER);
    }
}
