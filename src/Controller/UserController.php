<?php

namespace App\Controller;

use Adamski\Symfony\NotificationBundle\Helper\NotificationHelper;
use Adamski\Symfony\NotificationBundle\Model\Notification;
use Adamski\Symfony\NotificationBundle\Model\Type;
use Adamski\Symfony\TabulatorBundle\Adapter\Doctrine\RepositoryAdapter;
use Adamski\Symfony\TabulatorBundle\Column\DateTimeColumn;
use Adamski\Symfony\TabulatorBundle\Column\TextColumn;
use Adamski\Symfony\TabulatorBundle\Column\TickCrossColumn;
use Adamski\Symfony\TabulatorBundle\Column\TwigColumn;
use Adamski\Symfony\TabulatorBundle\Tabulator;
use Adamski\Symfony\TabulatorBundle\TabulatorFactory;
use App\Entity\User;
use App\Form\User\BaseType;
use App\Helper\SecurityHelper;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController {
    public function __construct(
        private readonly TabulatorFactory   $tabulatorFactory,
        private readonly UserRepository     $userRepository,
        private readonly SecurityHelper     $securityHelper,
        private readonly NotificationHelper $notificationHelper,
    ) {}

    #[Route("/{_locale}/user", name: "user", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        $userTable = $this->createTable();

        if (null !== ($tableResponse = $userTable->handleRequest($request))) {
            return $tableResponse;
        }

        return $this->render("modules/User/index.html.twig", [
            "table"         => $userTable->getConfig(),
            "search_config" => [[
                ["field" => "name", "type" => "like", "value" => "%"],
                ["field" => "emailAddress", "type" => "like", "value" => "%"],
                ["field" => "creationDate", "type" => "like", "value" => "%"],
            ]]
        ]);
    }

    #[Route("/{_locale}/user/create", name: "user.create", methods: ["GET", "POST"])]
    public function create(Request $request): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        $currentUser = new User();
        $createForm = $this->createCreateForm($currentUser);

        if ($request->isMethod("POST")) {
            $createForm->handleRequest($request);

            if ($createForm->isSubmitted() && $createForm->isValid()) {
                if (null !== $currentUser->getPlainPassword()) {
                    $this->securityHelper->upgradePassword($currentUser);
                }

                // Save changes and create notification
                $this->userRepository->persist($currentUser, true);
                $this->notificationHelper->add(
                    new Notification(Type::SUCCESS, "Account has been successfully created")
                );

                return $this->redirectToRoute("user");
            }
        }

        return $this->render("modules/User/create.html.twig", [
            "create_form" => $createForm->createView(),
        ]);
    }

    #[Route("/{_locale}/user/edit/{id}", name: "user.edit", requirements: ["id" => "^[0-9]+$"], methods: ["GET", "POST"])]
    public function edit(Request $request, int $id): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        if (null !== $currentUser = $this->userRepository->findOneBy(["id" => $id])) {
            $editForm = $this->createEditForm($currentUser);

            if ($request->isMethod("POST")) {
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    if (null !== $currentUser->getPlainPassword()) {
                        $this->securityHelper->upgradePassword($currentUser);
                    }

                    // Save changes and create notification
                    $this->userRepository->persist($currentUser, true);
                    $this->notificationHelper->add(
                        new Notification(Type::SUCCESS, "Changes were saved successfully")
                    );

                    return $this->redirectToRoute("user");
                }
            }

            return $this->render("modules/User/edit.html.twig", [
                "edit_form" => $editForm->createView(),
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route("/{_locale}/user/delete/{id}", name: "user.delete", requirements: ["id" => "^[0-9]+$"], methods: ["GET", "POST"])]
    public function delete(Request $request, int $id): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        if (null !== $currentUser = $this->userRepository->findOneBy(["id" => $id])) {
            $this->userRepository->remove($currentUser, true);
            $this->notificationHelper->add(
                new Notification(Type::SUCCESS, "Account has been successfully deleted")
            );

            return $this->redirectToRoute("user");
        }

        throw $this->createNotFoundException();
    }

    private function createTable(): Tabulator {
        return $this->tabulatorFactory
            ->create("#table")
            ->addColumn("name", TextColumn::class, [
                "title" => "Name",
            ])
            ->addColumn("emailAddress", TextColumn::class, [
                "title" => "Email Address",
                "extra" => [
                    "widthGrow" => 2
                ]
            ])
            ->addColumn("active", TickCrossColumn::class, [
                "title"        => "Status",
                "tickElement"  => '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">Active</span>',
                "crossElement" => '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">Disabled</span>',
            ])
            ->addColumn("creationDate", DateTimeColumn::class, [
                "title"  => "Created",
                "format" => "Y-m-d H:i",
            ])
            ->addColumn("action", TwigColumn::class, [
                "title"    => "Action",
                "passRow"  => true,
                "template" => "modules/User/table/action.html.twig",
                "extra"    => [
                    "headerSort" => false
                ]
            ])
            ->createAdapter(RepositoryAdapter::class, [
                "entity"        => User::class,
                "query_builder" => function (UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder("user");
                }
            ]);
    }

    /**
     * @param User $user
     *
     * @return FormInterface
     */
    private function createCreateForm(User $user): FormInterface {
        return $this->createForm(BaseType::class, $user, [
            "method" => "POST",
            "action" => $this->generateUrl("user.create"),
            "mode"   => "create",
        ]);
    }

    /**
     * @param User $user
     *
     * @return FormInterface
     */
    private function createEditForm(User $user): FormInterface {
        return $this->createForm(BaseType::class, $user, [
            "method" => "POST",
            "action" => $this->generateUrl("user.edit", ["id" => $user->getId()]),
            "mode"   => "edit",
        ]);
    }
}
