<?php

namespace App\Controller;

use App\Client\InfluxDbClient;
use App\Model\DTO\Agent\WritePayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController {
    public function __construct(
        private readonly InfluxDbClient $influxDbClient,
    ) {}

    #[Route("/agent/write", name: "agent.write", methods: ["POST"], format: "json")]
    public function write(#[MapRequestPayload] WritePayload $payload): JsonResponse {
        $this->influxDbClient->write(
            $this->influxDbClient->createPoint($payload->measurement)
                ->addTag("level", $payload->level)
                ->addTag("levelName", $payload->levelName)
                ->addTag("context", $payload->context)
                ->addField("message", $payload->message)
                ->time($payload->timestamp)
        );

        return new JsonResponse(["message" => "Ok"]);
    }
}
