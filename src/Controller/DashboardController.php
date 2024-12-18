<?php

namespace App\Controller;

use Adamski\Symfony\TabulatorBundle\Adapter\CallableAdapter;
use Adamski\Symfony\TabulatorBundle\AdapterQuery;
use Adamski\Symfony\TabulatorBundle\ArrayResult;
use Adamski\Symfony\TabulatorBundle\Column\DateTimeColumn;
use Adamski\Symfony\TabulatorBundle\Column\PropertyColumn;
use Adamski\Symfony\TabulatorBundle\Column\TextColumn;
use Adamski\Symfony\TabulatorBundle\Column\TwigColumn;
use Adamski\Symfony\TabulatorBundle\Filter\FilteringComparison;
use Adamski\Symfony\TabulatorBundle\Filter\FilteringType;
use Adamski\Symfony\TabulatorBundle\TabulatorFactory;
use App\Client\InfluxDbClient;
use App\Entity\Filter;
use App\Form\Dashboard\FilterType;
use App\Helper\EventHelper;
use App\Model\Filter\QuickRange;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController {
    public function __construct(
        private readonly EventHelper      $eventHelper,
        private readonly InfluxDbClient   $influxDbClient,
        private readonly TabulatorFactory $tabulatorFactory,
    ) {}

    #[Route("/{_locale}/dashboard", name: "dashboard", methods: ["GET", "POST"])]
    public function index(Request $request): Response {

        // Define Filter
        // For now there is no custom filters saving feature
        $filter = new Filter();
        $filter->setQuickRange(QuickRange::LAST_15_MINUTES);

        // Define Filter Form
        $filterForm = $this->createFilterForm($filter);

        // Define Tabulator Table
        $influxTable = $this->tabulatorFactory
            ->create("#table")
            ->setOptions([
                "placeholder" => "No Data Available",
                "pagination"  => false
            ])
            ->addColumn("client", PropertyColumn::class, [
                "title"     => "Client",
                "property"  => "name",
                "nullValue" => "Unknown"
            ])
            ->addColumn("measurement", TwigColumn::class, [
                "title"    => "Measurement",
                "field"    => "_measurement",
                "template" => "modules/Dashboard/table/measurement.html.twig"
            ])
            ->addColumn("level", TwigColumn::class, [
                "title"    => "Level",
                "passRow"  => true,
                "template" => "modules/Dashboard/table/level-name.html.twig"
            ])
            ->addColumn("message", TextColumn::class, [
                "title" => "Message",
                "extra" => [
                    "headerSort" => false,
                    "widthGrow"  => 3
                ]
            ])
            ->addColumn("dateTime", DateTimeColumn::class, [
                "title"  => "Created",
                "format" => "Y-m-d H:i:s"
            ])
            ->createAdapter(CallableAdapter::class, [
                "function" => function (AdapterQuery $adapterQuery) {
                    $filters = $adapterQuery->getFilteringBag()->getFilters(FilteringComparison::AND);
                    $filterStartDate = $this->getFilterDate($filters, FilteringType::GREATER) ?? Carbon::now()->subMinutes(15);
                    $filterEndDate = $this->getFilterDate($filters, FilteringType::LESS_OR_EQUAL) ?? new \DateTime();

                    $queryPart = [
                        'from(bucket: "params.bucketParam")',
                        'range(start: params.timeRangeStart, stop: params.timeRangeStop)'
                    ];

                    if ($adapterQuery->getFilteringBag()->hasFiltering()) {
                        foreach ($filters as $filter) {
                            if ($filter->getColumn()->getOption("field") !== "dateTime" && !empty($filter->getValue())) {
                                $queryPart[] = sprintf('filter(fn: (r) => r["%s"] == "%s")', $filter->getColumn()->getOption("field"), $filter->getValue());
                            }
                        }
                    }

                    // Create Query
                    $influxQuery = implode(" |> ", $queryPart);

//                    $queryResult = $this->influxDbClient->query(
//                        $this->influxDbClient->createQuery()
//                            ->setQuery('from(bucket: "params.bucketParam") |> range(start: params.timeRangeStart, stop: params.timeRangeStop)')
//                            ->setParams([
//                                "bucketParam"    => "default",
//                                "timeRangeStart" => \DateTime::createFromFormat("Y-m-d H:i:s", "2024-11-01 00:00:00", new \DateTimeZone("UTC"))->getTimestamp(),
//                                "timeRangeStop"  => \DateTime::createFromFormat("Y-m-d H:i:s", "2024-12-24 14:00:00", new \DateTimeZone("UTC"))->getTimestamp()
//                            ])
//                            ->compile()
//                    );

                    $queryResult = $this->influxDbClient->query(
                        $this->influxDbClient->createQuery()
                            ->setQuery($influxQuery)
                            ->setParams([
                                "bucketParam"    => $this->influxDbClient->getBucket() ?? "default",
                                "timeRangeStart" => $filterStartDate->getTimestamp(),
                                "timeRangeStop"  => $filterEndDate->getTimestamp()
                            ])
                            ->compile()
                    );

                    $queryResponse = $this->eventHelper->parseResponse($queryResult);

                    return new ArrayResult($queryResponse);
                }
            ]);

        // Handle Request
        if (null !== ($tableResponse = $influxTable->handleRequest($request))) {
            return $tableResponse;
        }

        return $this->render("modules/Dashboard/index.html.twig", [
            "table"       => $influxTable,
            "filter_form" => $filterForm->createView()
        ]);
    }

    /**
     * @param string $bucket
     * @return array
     */
    private function getMeasurements(string $bucket = "default"): array {
        $measurements = [];

        // https://community.influxdata.com/t/how-to-get-all-measurements-in-a-bucket/33595/2
        $measurementResult = $this->influxDbClient->query(
            "import \"influxdata/influxdb/schema\" \n schema.measurements(bucket: \"" . $bucket . "\")"
        );

        // Parse result
        foreach ($measurementResult as $result) {
            foreach ($result->records as $record) {
                $measurementName = $record->values["_value"];
                $measurements[$measurementName] = $measurementName;
            }
        }

        return $measurements;
    }

    private function getFilterDate(array $filters, FilteringType $condition): ?\DateTime {
        foreach ($filters as $filter) {
            if ($filter->getColumn()->getOption("field") === "dateTime" && $filter->getType() === $condition) {
                // return \DateTime::createFromFormat("Y-m-d H:i:s", $filter->getValue(), new \DateTimeZone("UTC"));
                return new \DateTime($filter->getValue());
            }
        }

        return null;
    }

    /**
     * @param Filter $filter
     * @return FormInterface
     */
    private function createFilterForm(Filter $filter): FormInterface {
        return $this->createForm(FilterType::class, $filter, [
            "measurements" => $this->getMeasurements(
                $this->influxDbClient->getBucket()
            ),
        ]);
    }
}
