<?php

namespace App\Client;

use App\Model\InfluxDb\ParameterizedQuery;
use InfluxDB2\Client;
use InfluxDB2\FluxTable;
use InfluxDB2\Model\Query;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use InfluxDB2\WriteType;

class InfluxDbClient {
    private ?Client $client = null;

    public function __construct(
        private readonly string $url,
        private readonly string $token,
        private readonly string $bucket,
        private readonly string $org,
    ) {}

    /**
     * Create InfluxDb Client.
     *
     * @return Client
     */
    public function createClient(): Client {
        if (null === $this->client) {
            $this->client = new Client([
                "url"       => $this->url,
                "token"     => $this->token,
                "bucket"    => $this->bucket,
                "org"       => $this->org,
                "precision" => WritePrecision::S
            ]);
        }

        return $this->client;
    }

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
     * Shortcut to create InfluxDb Query.
     *
     * @param array|null $data
     * @return ParameterizedQuery
     */
    public function createQuery(array $data = null): ParameterizedQuery {
        return new ParameterizedQuery($data);
    }
}
