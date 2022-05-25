<?php

namespace App\Controller;

use App\Calculator\SlotPriceCalculator;
use App\Entity\Option;
use App\Entity\Slot;
use App\Form\SlotType;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
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
        $rider = $this->getUser() ? $this->getUser()->getRider() : null;

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
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SlotPriceCalculator $slotPriceCalculator,
        MailerInterface $mailer,
        string $notificationEmail
    ): Response {
        $slot = new Slot();
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $rider = $user ? $user->getRider() : null;

            if ($rider) {
                $slot->setRider($rider);
            }

            $slot->setPrice($slotPriceCalculator->calculateSlotPrice($slot));
            $entityManager->persist($slot);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->subject('Zarezerwowano pływanie na Wakepark Wrocław')
                ->htmlTemplate('emails/slot_registration.html.twig')
                ->context([
                    'name' => $user->getFirstname(),
                    'slot' => $slot
                ]);

            $mailer->send($email);

            $adminEmail = (new TemplatedEmail())
                ->to($notificationEmail)
                ->subject('Zarezerwowano slot na pływanie')
                ->htmlTemplate('emails/slot_registration_admin.html.twig')
                ->context([
                    'user' => $user,
                    'slot' => $slot
                ]);

            $mailer->send($adminEmail);
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
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/{id}", name="app_slot_delete", methods={"POST"})
     */
    public function delete(Request $request, Slot $slot, EntityManagerInterface $entityManager): Response
    {
        if (
            $this->isCsrfTokenValid('delete'.$slot->getId(), $request->request->get('_token'))
            && $this->getUser()->getRider() == $slot->getRider()
        ) {
            $entityManager->remove($slot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_slot_index', [], Response::HTTP_SEE_OTHER);
    }
}
