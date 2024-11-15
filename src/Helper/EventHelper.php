<?php

namespace App\Helper;

use App\Model\Event\Event;
use InfluxDB2\FluxTable;

class EventHelper {

    /**
     * Parse InfluxDb Query Result.
     *
     * @param array $responseItems
     * @return array
     * @throws \DateMalformedStringException
     */
    public function parseResponse(array $responseItems): array {
        $response = [];

        foreach ($responseItems as $responseItem) {
            if ($responseItem instanceof FluxTable) {
                foreach ($responseItem->records as $record) {
                    $response[] = (new Event())
                        ->setMeasurement($record->values["_measurement"])
                        ->setLevel($record->values["level"])
                        ->setLevelName($record->values["level_name"])
                        ->setMessage($record->values["_value"])
                        ->setContext($this->decodeContext($record->values["context"]))
                        ->setDateTime(new \DateTime($record->values["_time"]));
                }
            }
        }

        return $response;
    }

    /**
     * Encode Context.
     *
     * @param array $context
     * @param bool  $replaceQuot
     * @return string|null
     */
    public function encodeContext(array $context, bool $replaceQuot = true): ?string {
        $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;

        if ($replaceQuot) {
            array_walk_recursive($context, function (&$value, $key) {
                if (is_string($value)) {
                    $value = str_replace("\"", "'", $value);
                };
            });
        }

        if (false !== ($encodedContext = json_encode($context, $jsonFlags))) {
            return $encodedContext;
        }

        return null;
    }

    /**
     * Decode Context.
     *
     * @param string $context
     * @return array|null
     */
    public function decodeContext(string $context): ?array {
        return json_decode($context, true);
    }
}
