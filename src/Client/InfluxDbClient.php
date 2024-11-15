<?php

namespace App\Client;

use App\Entity\Event;
use App\Model\InfluxDb\ParameterizedQuery;
use InfluxDB2\Client;
use InfluxDB2\FluxTable;
use InfluxDB2\Model\Query;
use InfluxDB2\Point;
use InfluxDB2\WriteType;

class InfluxDbClient {
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Send Query.
     *
     * @param string|Query $query
     * @return FluxTable[]|null
     */
    public function query(string|Query $query): ?array {
        $queryClient = $this->createClient()->createQueryApi();

        return $queryClient->query($query);
    }

    /**
     * Write data.
     *
     * @param string|Point $data
     * @param int          $writeType
     * @param bool         $flush
     * @return void
     */
    public function write(string|Point $data, int $writeType = WriteType::SYNCHRONOUS, bool $flush = true): void {
        $writeClient = $this->createClient()->createWriteApi(["writeType" => $writeType]);
        $writeClient->write($data);

        if ($flush) {
            $writeClient->close();
        }
    }

    /**
     * Shortcut to create InfluxDb Point.
     *
     * @param string $measurement
     * @return Point
     */
    public function createPoint(string $measurement): Point {
        return new Point($measurement);
    }

    /**
     * Create an instance of the InfluxDb Point based on provided Event.
     *
     * @param Event $event
     * @return Point
     */
    public function createPointFromEvent(Event $event): Point {
        return (new Point($event->getMeasurement()))
            ->addTag("event_id", $event->getId())
            ->addTag("level", $event->getLevel())
            ->addTag("level_name", $event->getLevelName())
            ->addField("message", $event->getMessage())
            ->time($event->getTimestamp());
    }

    /**
     * Shortcut to create InfluxDb Query.
     *
     * @param array|null $data
     * @return ParameterizedQuery
     */
    public function createQuery(array $data = null): ParameterizedQuery {
        return new ParameterizedQuery($data);
    }

    /**
     * @return Client
     */
    private function createClient(): Client {
        return $this->client;
    }
}
