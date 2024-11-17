import {readAttribute} from "./functions/read-attribute.js";
import {initTabulator} from "./plugins/tabulator-table.js";

let tableConfigAttr = readAttribute("table-config");

if (null !== tableConfigAttr) {
    let tableConfig = JSON.parse(tableConfigAttr);

    // Init Tabulator
    initTabulator(tableConfig);
}
