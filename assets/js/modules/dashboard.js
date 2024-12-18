import moment from "moment";
import { TabulatorFull as Tabulator } from "tabulator-tables";
import { readAttribute } from "../functions/read-attribute.js";
import { initTabulator } from "../../../vendor/m-adamski/symfony-tabulator-bundle/src/Resources/public/js/tabulator.js";

let tableConfigAttr = readAttribute("table-config");

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

    // Init Tabulator
    initTabulator(Tabulator, tableConfig).then((tabulator) => {

        filterSubmitButton.addEventListener("click", (event) => {
            let tableFilter = [
                { field: "client", type: "=", value: filterClientInput.value },
                { field: "measurement", type: "=", value: filterMeasurementInput.value },
                { field: "level", type: "=", value: filterLevelInput.value },
            ];

            switch (filterQuickRangeInput.value) {
                case "last_5_minutes":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(5, "minutes") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "last_15_minutes":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(15, "minutes") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "last_30_minutes":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(30, "minutes") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "last_hour":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(1, "hour") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "last_12_hours":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(12, "hours") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "last_24_hours":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(24, "hours") },
                        { field: "dateTime", type: "<=", value: moment() },
                    ]);
                    break;
                case "today":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().startOf("day") },
                        { field: "dateTime", type: "<=", value: moment().endOf("day") },
                    ]);
                    break;
                case "yesterday":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(1, "day").startOf("day") },
                        { field: "dateTime", type: "<=", value: moment().subtract(1, "day").endOf("day") },
                    ]);
                    break;
                case "last_7_days":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(7, "days").startOf("day") },
                        { field: "dateTime", type: "<=", value: moment().endOf("day") },
                    ]);
                    break;
                case "last_30_days":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(30, "days").startOf("day") },
                        { field: "dateTime", type: "<=", value: moment().endOf("day") },
                    ]);
                    break;
                case "last_90_days":
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment().subtract(90, "days").startOf("day") },
                        { field: "dateTime", type: "<=", value: moment().endOf("day") },
                    ]);
                    break;
                default:
                    tableFilter = tableFilter.concat([
                        { field: "dateTime", type: ">", value: moment(filterStartDateInput.value) },
                        { field: "dateTime", type: "<=", value: moment(filterEndDateInput.value) },
                    ]);
            }

            tabulator.setFilter(tableFilter);
        });
    });
}
