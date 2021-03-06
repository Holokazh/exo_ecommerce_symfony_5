<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use App\Controller\Admin\OrderCrudController;
use App\Entity\Header;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Klevor');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur(s)', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Commande(s)', 'fas fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Categorie(s)', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Produit(s)', 'fas fa-tag', Product::class);
        yield MenuItem::linkToCrud('Transporteur(s)', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('En-tête', 'fas fa-desktop', Header::class);
    }
}
