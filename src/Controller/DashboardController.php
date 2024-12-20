<?php

namespace App\Controller;

use Adamski\Symfony\TabulatorBundle\Adapter\Doctrine\RepositoryAdapter;
use Adamski\Symfony\TabulatorBundle\AdapterQuery;
use Adamski\Symfony\TabulatorBundle\Column\DateTimeColumn;
use Adamski\Symfony\TabulatorBundle\Column\PropertyColumn;
use Adamski\Symfony\TabulatorBundle\Column\TextColumn;
use Adamski\Symfony\TabulatorBundle\Column\TwigColumn;
use Adamski\Symfony\TabulatorBundle\TabulatorFactory;
use App\Entity\Filter;
use App\Form\Dashboard\FilterType;
use App\Model\Filter\QuickRange;
use App\Repository\EventRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController {
    public function __construct(
        private readonly EventRepository  $eventRepository,
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
        $eventTable = $this->tabulatorFactory
            ->create("#table")
            ->setOptions([
                "placeholder"   => "No Data Available",
                "initialFilter" => [
                    ["field" => "timestamp", "type" => ">", "value" => Carbon::now()->subMinutes(15)->toIso8601String()],
                    ["field" => "timestamp", "type" => "<=", "value" => Carbon::now()->toIso8601String()],
                ]
            ])
            ->addColumn("client", PropertyColumn::class, [
                "title"     => "Client",
                "property"  => "name",
                "nullValue" => "Unknown"
            ])
            ->addColumn("measurement", TwigColumn::class, [
                "title"    => "Measurement",
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
            ->addColumn("timestamp", DateTimeColumn::class, [
                "title"  => "Timestamp",
                "format" => "Y-m-d H:i:s",
                "extra"  => [
                    "widthGrow" => 1.5
                ]
            ])
            ->addColumn("action", TwigColumn::class, [
                "title"    => false,
                "passRow"  => true,
                "template" => "modules/Dashboard/table/action.html.twig",
                "extra"    => [
                    "headerSort" => false
                ]
            ])
            ->createAdapter(RepositoryAdapter::class, [
                "entity"        => "App\Entity\Event",
                "query_builder" => function (EventRepository $repository, AdapterQuery $adapterQuery) {
                    return $repository->createQueryBuilder("event");
                }
            ]);

        // Handle Request
        if (null !== ($tableResponse = $eventTable->handleRequest($request))) {
            return $tableResponse;
        }

        return $this->render("modules/Dashboard/index.html.twig", [
            "table"       => $eventTable,
            "filter_form" => $filterForm->createView(),
            "timezone"    => date_default_timezone_get(),
        ]);
    }

    #[Route("/{_locale}/dashboard/event", name: "dashboard.event", methods: ["POST"], format: "json")]
    public function event(Request $request): Response {
        if (null !== ($id = $request->getPayload()->get("id"))) {
            if (null !== ($event = $this->eventRepository->findOneBy(["id" => $id]))) {
                return $this->render("modules/Dashboard/event-modal.html.twig", [
                    "event" => $event,
                ]);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @param Filter $filter
     * @return FormInterface
     */
    private function createFilterForm(Filter $filter): FormInterface {
        $availableMeasurements = $this->eventRepository->getMeasurements();

        return $this->createForm(FilterType::class, $filter, [
            "measurements" => array_combine($availableMeasurements, $availableMeasurements),
        ]);
    }
}
