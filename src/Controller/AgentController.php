<?php

namespace App\Controller;

use App\Client\InfluxDbClient;
use App\Entity\Client;
use App\Helper\EventHelper;
use App\Model\DTO\Agent\WritePayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController {
    public function __construct(
        private readonly EventHelper    $eventHelper,
        private readonly InfluxDbClient $influxDbClient,
    ) {}

    #[Route("/agent/write", name: "agent.write", methods: ["POST"], format: "json")]
    public function write(#[MapRequestPayload] WritePayload $payload): JsonResponse {

        /** @var Client $client */
        if (null !== ($client = $this->getUser())) {
            $this->influxDbClient->write(
                $this->influxDbClient->createPoint($payload->getMeasurement())
                    ->addTag("level", $payload->level)
                    ->addTag("level_name", $payload->levelName)
                    ->addTag("client", $client->getId())
                    ->addTag("context", $this->eventHelper->encodeContext($payload->getContext()))
                    ->addField("message", $payload->message)
                    ->time($payload->timestamp)
            );

            return new JsonResponse(["message" => "Ok"]);
        }

        throw $this->createAccessDeniedException();
    }
}
