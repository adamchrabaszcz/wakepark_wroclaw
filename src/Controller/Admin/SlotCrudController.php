<?php

namespace App\Controller\Admin;

use App\Calculator\SlotPriceCalculator;
use App\Controller\Admin\Filter\DateCalendarFilter;
use App\Entity\Slot;
use App\Form\RiderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class SlotCrudController extends AbstractCrudController
{
    private SlotPriceCalculator $slotPriceCalculator;

    public function __construct(SlotPriceCalculator $slotPriceCalculator)
    {
        $this->slotPriceCalculator = $slotPriceCalculator;
    }

    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('beginAt')
                ->renderAsChoice(true)
                ->setColumns(12)
                ->setFormTypeOptions([
                    'minutes' => [0, 15, 30, 45],
                    'hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                ]),
            DateTimeField::new('endAt')
                ->renderAsChoice(true)
                ->setColumns(12)
                ->setFormTypeOptions([
                    'minutes' => [0, 15, 30, 45],
                    'hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                ]),
            IntegerField::new('price')->hideWhenCreating(),
            IntegerField::new('fullPrice')->onlyOnIndex(),
            AssociationField::new('rider')
                ->autocomplete()
                ->setColumns(5),
            CollectionField::new('newRider')
                ->setEntryType(RiderType::class)
                ->onlyOnForms()
                ->setColumns(7),
            AssociationField::new('options'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateCalendarFilter::new('beginAt'))
            ->add('options')
            ->add('rider')
            ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPrice($this->slotPriceCalculator->calculateSlotPrice($entityInstance));
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return Crud::new()
            ->overrideTemplate('crud/index', 'admin/slot/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/slot/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/slot/edit.html.twig')
            ->setDefaultSort(['beginAt' => 'DESC'])
            ;
    }

    public function createEntity(string $entityFqcn) {
        $entity = new $entityFqcn();
        $entity->setBeginAt((new DateTime('now'))->setTime(9, 0));
        $entity->setEndAt((new DateTime('now'))->setTime(9, 15));
        return $entity;
    }
}
