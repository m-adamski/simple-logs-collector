<?php

namespace App\Model\InfluxDb;

use InfluxDB2\FluxQueryError;
use InfluxDB2\Model\Query;

class ParameterizedQuery extends Query {
    public function compile(): Query {
        $query = $this->getQuery();
        $params = $this->getParams();

        // https://docs.influxdata.com/influxdb/cloud/query-data/parameterized-queries/
        // Only InfluxDB Cloud supports parameterized Flux queries.
        // Use Regex to find and replace all parameters.
        $paramsRegex = "/params\.([a-zA-Z]+)/m";
        $compileQuery = preg_replace_callback($paramsRegex, function ($match) use ($params) {
            if (!array_key_exists($match[1], $params)) {
                throw new FluxQueryError("Missing parameter '{$match[1]}'");
            }

            return $params[$match[1]];
        }, $query);

        // Overwrite query and params
        $this->setQuery($compileQuery);
        $this->setParams([]);

        return $this;
    }
}
