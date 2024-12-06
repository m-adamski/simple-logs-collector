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
use App\Entity\Client;
use App\Form\Client\BaseType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController {
    public function __construct(
        private readonly TabulatorFactory   $tabulatorFactory,
        private readonly ClientRepository   $clientRepository,
        private readonly NotificationHelper $notificationHelper,
    ) {}

    #[Route("/{_locale}/client", name: "client", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        $clientTable = $this->createTable();

        if (null !== ($tableResponse = $clientTable->handleRequest($request))) {
            return $tableResponse;
        }

        return $this->render("modules/Client/index.html.twig", [
            "table"         => $clientTable->getConfig(),
            "search_config" => [[
                ["field" => "name", "type" => "like", "value" => "%"],
                ["field" => "secretToken", "type" => "like", "value" => "%"],
                ["field" => "creationDate", "type" => "like", "value" => "%"],
            ]]
        ]);
    }

    #[Route("/{_locale}/client/create", name: "client.create", methods: ["GET", "POST"])]
    public function create(Request $request): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        $currentClient = new Client();
        $currentClient->setSecretToken(bin2hex(openssl_random_pseudo_bytes(32)));
        $createForm = $this->createCreateForm($currentClient);

        if ($request->isMethod("POST")) {
            $createForm->handleRequest($request);

            if ($createForm->isSubmitted() && $createForm->isValid()) {
                $this->clientRepository->persist($currentClient, true);
                $this->notificationHelper->add(
                    new Notification(Type::SUCCESS, "Client has been successfully created")
                );

                return $this->redirectToRoute("client");
            }
        }

        return $this->render("modules/Client/create.html.twig", [
            "create_form" => $createForm->createView(),
        ]);
    }

    #[Route("/{_locale}/client/edit/{id}", name: "client.edit", requirements: ["id" => "^[0-9]+$"], methods: ["GET", "POST"])]
    public function edit(Request $request, int $id): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        if (null !== ($currentClient = $this->clientRepository->findOneBy(["id" => $id]))) {
            $editForm = $this->createEditForm($currentClient);

            if ($request->isMethod("POST")) {
                $editForm->handleRequest($request);

                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->clientRepository->persist($currentClient, true);
                    $this->notificationHelper->add(
                        new Notification(Type::SUCCESS, "Changes were saved successfully")
                    );

                    return $this->redirectToRoute("client");
                }
            }

            return $this->render("modules/Client/edit.html.twig", [
                "edit_form" => $editForm->createView(),
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route("/{_locale}/client/delete/{id}", name: "client.delete", requirements: ["id" => "^[0-9]+$"], methods: ["GET", "POST"])]
    public function delete(Request $request, int $id): Response {
        $this->denyAccessUnlessGranted("ROLE_ADMINISTRATOR");

        if (null !== ($currentClient = $this->clientRepository->findOneBy(["id" => $id]))) {
            $this->clientRepository->remove($currentClient, true);
            $this->notificationHelper->add(
                new Notification(Type::SUCCESS, "Client has been successfully deleted")
            );

            return $this->redirectToRoute("client");
        }

        throw $this->createNotFoundException();
    }

    private function createTable(): Tabulator {
        return $this->tabulatorFactory
            ->create("#table")
            ->addColumn("name", TextColumn::class, [
                "title" => "Name",
            ])
            ->addColumn("secretToken", TwigColumn::class, [
                "title"    => "Token",
                "template" => "modules/Client/table/secret-token.html.twig",
                "extra"    => [
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
                "template" => "modules/Client/table/action.html.twig",
                "extra"    => [
                    "headerSort" => false
                ]
            ])
            ->createAdapter(RepositoryAdapter::class, [
                "entity"        => Client::class,
                "query_builder" => function (ClientRepository $clientRepository) {
                    return $clientRepository->createQueryBuilder("client");
                }
            ]);
    }

    /**
     * @param Client $client
     *
     * @return FormInterface
     */
    private function createCreateForm(Client $client): FormInterface {
        return $this->createForm(BaseType::class, $client, [
            "method" => "POST",
            "action" => $this->generateUrl("client.create"),
        ]);
    }

    /**
     * @param Client $client
     *
     * @return FormInterface
     */
    private function createEditForm(Client $client): FormInterface {
        return $this->createForm(BaseType::class, $client, [
            "method" => "POST",
            "action" => $this->generateUrl("client.edit", ["id" => $client->getId()]),
        ]);
    }
}
