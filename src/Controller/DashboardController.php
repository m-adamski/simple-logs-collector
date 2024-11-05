<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController {
    #[Route("/{_locale}/dashboard", name: "dashboard", methods: ["GET"])]
    public function index(): Response {
        return $this->render("modules/Dashboard/index.html.twig");
    }
}
