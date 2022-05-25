<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Entity\Rider;
use App\Entity\Slot;
use App\Entity\User;
use App\Repository\SlotRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    private ChartBuilderInterface $chartBuilder;
    private SlotRepository $slotRepository;

    public function __construct(ChartBuilderInterface $chartBuilder, SlotRepository $slotRepository)
    {
        $this->chartBuilder = $chartBuilder;
        $this->slotRepository = $slotRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $chartSlots = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartTotalEarnings = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartSlotsMonth = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartTotalEarningsMonth = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartTotalEarningsYear = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $data = [];
        $dataTotalEarnings = [];
        $dataSlotsMonth = [];
        $dataTotalEarningsMonth = [];
        $dataTotalEarningsYear = [];

        foreach ($this->slotRepository->findNumberOfSlotsThisWeek() as $slot) {
            $data[$slot['day']] = $slot['count'];
        }

        foreach ($this->slotRepository->findTotalEarningsThisWeek() as $slot) {
            $dataTotalEarnings[$slot['day']] = $slot['price'];
        }

        foreach ($this->slotRepository->findNumberOfSlotsThisMonth() as $slot) {
            $dataSlotsMonth[$slot['day']] = $slot['count'];
        }

        foreach ($this->slotRepository->findTotalEarningsThisMonth() as $slot) {
            $dataTotalEarningsMonth[$slot['day']] = $slot['price'];
        }

        foreach ($this->slotRepository->findTotalEarningsThisYear() as $slot) {
            $dataTotalEarningsYear[$slot['month']] = $slot['price'];
        }

        $chartSlots->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Slots this week (number)',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                ],
            ],
        ]);
        $chartSlots->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 40,
                ],
            ],
        ]);

        $chartTotalEarnings->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total earnings this week (PLN)',
                    'backgroundColor' => 'rgb(255, 100, 0)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $dataTotalEarnings,
                ],
            ],
        ]);
        $chartTotalEarnings->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 2000,
                ],
            ],
        ]);

        $chartSlotsMonth->setData([
            'datasets' => [
                [
                    'label' => 'Slots this month (number)',
                    'backgroundColor' => 'rgb(255, 100, 255)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $dataSlotsMonth,
                ],
            ],
        ]);
        $chartSlotsMonth->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 40,
                ],
            ],
        ]);

        $chartTotalEarningsMonth->setData([
            'datasets' => [
                [
                    'label' => 'Total earnings this month (PLN)',
                    'backgroundColor' => 'rgb(100, 99, 255)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $dataTotalEarningsMonth,
                ],
            ],
        ]);
        $chartTotalEarningsMonth->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 2000,
                ],
            ],
        ]);

        $chartTotalEarningsYear->setData([
            'datasets' => [
                [
                    'label' => 'Total earnings monthly (PLN)',
                    'backgroundColor' => 'rgb(100, 99, 255)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $dataTotalEarningsYear,
                ],
            ],
        ]);
        $chartTotalEarningsYear->setOptions([
//            'scales' => [
//                'y' => [
//                    'suggestedMin' => 0,
//                    'suggestedMax' => 30000,
//                ],
//            ],
        ]);

        return $this->render('admin/dashboard.html.twig', [
            'chartSlots' => $chartSlots,
            'chartTotalEarnings' => $chartTotalEarnings,
            'chartSlotsMonth' => $chartSlotsMonth,
            'chartTotalEarningsMonth' => $chartTotalEarningsMonth,
            'chartTotalEarningsYear' => $chartTotalEarningsYear,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Wakepark');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Slots'),
            MenuItem::linkToCrud('Slots', 'fa fa-calendar-check', Slot::class),
            MenuItem::linkToCrud('Options', 'fa fa-sitemap', Option::class),

            MenuItem::section('Riders'),
            MenuItem::linkToCrud('Riders', 'fa fa-user', Rider::class),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class)
                ->setPermission('ROLE_ADMIN'),
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToRoute('Frontend', 'fa fa-id-card', 'app_slot_calendar', ),
            ]);
    }
}
