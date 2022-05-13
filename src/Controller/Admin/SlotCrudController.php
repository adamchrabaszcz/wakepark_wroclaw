<?php

namespace App\Controller\Admin;

use App\Calculator\SlotPriceCalculator;
use App\Controller\Admin\Filter\DateCalendarFilter;
use App\Entity\Slot;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            DateTimeField::new('beginAt'),
            DateTimeField::new('endAt'),
            IntegerField::new('price')->hideOnForm(),
            AssociationField::new('rider'),
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
            ->setDefaultSort(['beginAt' => 'DESC'])
            ;
    }
}
