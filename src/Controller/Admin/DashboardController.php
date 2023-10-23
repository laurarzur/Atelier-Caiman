<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Realisation;
use App\Entity\Serie;
use App\Entity\Transporteur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/administration', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(CategorieCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Atelier Caïman');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Catégories à la vente', 'fa-solid fa-swatchbook', Categorie::class);
        yield MenuItem::linkToCrud('Pièces en vente', 'fa-solid fa-cart-shopping', Produit::class);
        yield MenuItem::linkToCrud('Séries sur-mesure', 'fa-solid fa-folder-tree', Serie::class);
        yield MenuItem::linkToCrud('Réalisations sur mesure', 'fa-solid fa-puzzle-piece', Realisation::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fa-solid fa-truck', Transporteur::class);
    }
}
