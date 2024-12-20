import moment from "moment";
import "moment-timezone";
import { TabulatorFull as Tabulator } from "tabulator-tables";
import { readAttribute } from "../functions/read-attribute.js";
import { initTabulator } from "../../../vendor/m-adamski/symfony-tabulator-bundle/src/Resources/public/js/tabulator.js";

// Read Attributes
const timezone = readAttribute("timezone");
const tableConfigAttr = readAttribute("table-config");
const eventFetchUrl = readAttribute("event-fetch-url");

if (null !== tableConfigAttr) {
    let tableConfig = JSON.parse(tableConfigAttr);

    // Define Filter Inputs
    const filterClientInput = document.querySelector("select[name='filter[client]']");
    const filterMeasurementInput = document.querySelector("select[name='filter[measurement]']");
    const filterLevelInput = document.querySelector("select[name='filter[level]']");
    const filterStartDateInput = document.querySelector("input[name='filter[startDate]']");
    const filterEndDateInput = document.querySelector("input[name='filter[endDate]']");
    const filterQuickRangeInput = document.querySelector("select[name='filter[quickRange]']");
    const filterSubmitButton = document.querySelector("button[name='filter[submit]']");

    // Define Event Modal
    const eventModal = document.getElementById("event-modal");
    const eventModalPlaceholder = document.querySelector("div[data-event-modal-placeholder]");
    const eventModalContent = document.querySelector("div[data-event-modal-content]");

    // Init Tabulator
    initTabulator(Tabulator, tableConfig).then((tabulator) => {

        // tabulator.on("dataLoaded", function (data) {
        //     console.log(data);
        // });

        filterSubmitButton.addEventListener("click", (event) => {
            let filterStartDate = null;
            let filterEndDate = null;

            switch (filterQuickRangeInput.value) {
                case "last_5_minutes":
                    filterStartDate = moment().subtract(5, "minutes");
                    filterEndDate = moment();
                    break;
                case "last_15_minutes":
                    filterStartDate = moment().subtract(15, "minutes");
                    filterEndDate = moment();
                    break;
                case "last_30_minutes":
                    filterStartDate = moment().subtract(30, "minutes");
                    filterEndDate = moment();
                    break;
                case "last_hour":
                    filterStartDate = moment().subtract(1, "hour");
                    filterEndDate = moment();
                    break;
                case "last_12_hours":
                    filterStartDate = moment().subtract(12, "hours");
                    filterEndDate = moment();
                    break;
                case "last_24_hours":
                    filterStartDate = moment().subtract(24, "hours");
                    filterEndDate = moment();
                    break;
                case "today":
                    filterStartDate = moment().startOf("day");
                    filterEndDate = moment().endOf("day");
                    break;
                case "yesterday":
                    filterStartDate = moment().subtract(1, "day").startOf("day");
                    filterEndDate = moment().subtract(1, "day").endOf("day");
                    break;
                case "last_7_days":
                    filterStartDate = moment().subtract(7, "days").startOf("day");
                    filterEndDate = moment().endOf("day");
                    break;
                case "last_30_days":
                    filterStartDate = moment().subtract(30, "days").startOf("day");
                    filterEndDate = moment().endOf("day");
                    break;
                case "last_90_days":
                    filterStartDate = moment().subtract(90, "days").startOf("day");
                    filterEndDate = moment().endOf("day");
                    break;
                default:
                    filterStartDate = moment(filterStartDateInput.value);
                    filterEndDate = moment(filterEndDateInput.value);
            }

            // Define Tabulator filters
            let tableFilter = [
                { field: "client", type: "=", value: filterClientInput.value },
                { field: "measurement", type: "=", value: filterMeasurementInput.value },
                { field: "level", type: "=", value: filterLevelInput.value },
                { field: "timestamp", type: ">", value: filterStartDate.tz(timezone).format() },
                { field: "timestamp", type: "<=", value: filterEndDate.tz(timezone).format() },
            ];

            tabulator.setFilter(tableFilter);
        });

        // Reinit
        let renderTimeout = null;

        tabulator.on("renderComplete", () => {
            clearTimeout(renderTimeout);

            renderTimeout = setTimeout(() => {
                window.HSStaticMethods.autoInit();

                document.querySelectorAll("button[data-event]").forEach((actionButton) => {
                    actionButton.addEventListener("click", async (event) => {
                        event.preventDefault();

                        const eventId = actionButton.dataset["event"];

                        // Show placeholder
                        eventModalPlaceholder.classList.remove("hidden");
                        eventModalContent.classList.add("hidden");

                        // Fetch data
                        const response = await fetch(eventFetchUrl, {
                            method: "POST",
                            body: JSON.stringify({ id: eventId }),
                        });

                        if (response.ok) {
                            eventModalContent.innerHTML = await response.text();
                            eventModalContent.classList.remove("hidden");
                            eventModalPlaceholder.classList.add("hidden");
                        }
                    })
                });

            }, 500);
        });
    });
}
