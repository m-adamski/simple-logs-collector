<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController {
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(): RedirectResponse {
        return $this->redirectToRoute("dashboard", ["_locale" => "en"]);
    }

    #[Route("/healthcheck", name: "healthcheck", methods: ["GET"])]
    public function healthCheck(): JsonResponse {
        return new JsonResponse(["message" => "Ok"]);
    }
}
