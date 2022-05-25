<?php

namespace App\EventSubscriber;

use App\Controller\Admin\SlotCrudController;
use App\Repository\SlotRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $slotRepository;
    private $router;
    private AdminUrlGenerator $adminUrlGenerator;
    private Security $security;

    public function __construct(
        SlotRepository        $slotRepository,
        UrlGeneratorInterface $router,
        AdminUrlGenerator $adminUrlGenerator,
        Security $security
    ) {
        $this->slotRepository = $slotRepository;
        $this->router = $router;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $slots = $this->slotRepository
            ->createQueryBuilder('slot')
            ->where('slot.beginAt BETWEEN :start and :end OR slot.endAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($slots as $slot) {
            // this create the events with your data (here booking data) to fill calendar
            $slotEvent = new Event(
                $slot->getRider() ? $slot->getRider()->getShort() : '',
                $slot->getBeginAt(),
                $slot->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $slotEvent->addOption(
                'url',
                $this->security->isGranted('ROLE_ADMIN', $this->security->getUser())
                    ? $this->adminUrlGenerator
                        ->setController(SlotCrudController::class)
                        ->setAction('edit')
                        ->setEntityId($slot->getId())
                        ->generateUrl()
                    : $this->router->generate('app_slot_show', [
                        'id' => $slot->getId(),
                    ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($slotEvent);
        }
    }
}