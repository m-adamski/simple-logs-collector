<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Event;
use App\Model\DTO\Agent\WritePayload;
use App\Model\Event\Level;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController {
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    #[Route("/agent/write", name: "agent.write", methods: ["POST"], format: "json")]
    public function write(#[MapRequestPayload] WritePayload $payload): JsonResponse {

        /** @var Client $client */
        if (null !== ($client = $this->getUser())) {
            $event = new Event();
            $event->setClient($client);
            $event->setMeasurement($payload->getMeasurement());
            $event->setContext($payload->getContext());
            $event->setMessage($payload->getMessage());
            $event->setLevel(Level::from($payload->getLevel()));
            $event->setTimestamp(
                (new \DateTime())
                    ->setTimestamp($payload->getTimestamp())
            );

            // Flush changes
            $this->eventRepository->persist($event, true);

            return new JsonResponse(["message" => "Ok"]);
        }

        throw $this->createAccessDeniedException();
    }
}
