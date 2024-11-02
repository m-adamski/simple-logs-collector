<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Security\AuthenticationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {
    #[Route("/{_locale}/auth/login", name: "security.auth.login", methods: ["GET", "POST"])]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute("dashboard");
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("modules/Security/login.html.twig", [
            "auth_form"     => $this->createAuthForm($lastUsername)->createView(),
            "last_username" => $lastUsername,
            "error"         => $error
        ]);
    }

    #[Route("/{_locale}/auth/logout", name: "security.auth.logout", methods: ["GET"])]
    public function logout(): void {
        // The Controller can be blank: it will never be called!
    }

    /**
     * @param string $lastUsername
     * @return FormInterface
     */
    private function createAuthForm(string $lastUsername): FormInterface {
        return $this->createForm(AuthenticationType::class, ["last_username" => $lastUsername], [
            "method"        => "POST",
            "action"        => $this->generateUrl("security.auth.login"),
            "last_username" => $lastUsername
        ]);
    }
}
