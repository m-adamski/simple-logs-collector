<?php

namespace App\Controller;

use Adamski\Symfony\NotificationBundle\Helper\NotificationHelper;
use Adamski\Symfony\NotificationBundle\Model\Notification;
use Adamski\Symfony\NotificationBundle\Model\Type;
use App\Entity\Client;
use App\Form\Client\BaseType;
use App\Model\Tabulator\Adapter\StaticAdapter;
use App\Model\Tabulator\Column\TextColumn;
use App\Model\Tabulator\TabulatorFactory;
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

        $clientTable = $this->tabulatorFactory
            ->create("#table")
            ->setOptions([
//                "ajaxConfig"      => "GET",
//                "ajaxContentType" => "form",
                "paginationSize" => 3,
            ])
            ->addColumn("name", TextColumn::class, [
                "title" => "Name",
                "field" => "name",
            ])
            ->addColumn("token", TextColumn::class, [
                "title"     => "Token",
                "field"     => "token",
                "widthGrow" => 2
            ])
            ->addColumn("status", TextColumn::class, [
                "title" => "Status",
                "field" => "status",
            ])
            ->addColumn("creationDate", TextColumn::class, [
                "title" => "Created",
                "field" => "creationDate",
            ])
            ->setAdapter((new StaticAdapter())
                ->setData([
                    ["id" => 1, "name" => "Test1", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 2, "name" => "Test2", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 3, "name" => "Test3", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 4, "name" => "Test4", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 5, "name" => "Test5", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 6, "name" => "Test6", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 7, "name" => "Test7", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 8, "name" => "Test8", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 9, "name" => "Test9", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 10, "name" => "Test10", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 11, "name" => "Test11", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 12, "name" => "Test12", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 13, "name" => "Test13", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 14, "name" => "Test14", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                    ["id" => 15, "name" => "Test15", "token" => "A", "status" => "Ok", "creationDate" => "Ok"],
                ])
            );

        if (null !== ($tableResponse = $clientTable->handleRequest($request))) {
            return $tableResponse;
        }

        return $this->render("modules/Client/index.html.twig", [
            "table"   => $clientTable->getConfig(),
            "clients" => $this->clientRepository->findAll(),
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
