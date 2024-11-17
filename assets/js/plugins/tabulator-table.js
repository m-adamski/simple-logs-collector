// import "tabulator-tables/dist/css/tabulator.min.css";
import {TabulatorFull as Tabulator} from "tabulator-tables";

const initTabulator = (tableConfig) => {
    let table = new Tabulator(tableConfig.selector, tableConfig.options);

    // console.log(tableConfig.options);

    // Overwrite ajaxContentType to send X-Request-Generator header
    // https://github.com/olifolkerd/tabulator/blob/master/src/js/modules/Ajax/defaults/contentTypeFormatters.js
    if (table.options["ajaxContentType"] === "json") {
        console.log("Overwrite");
        table.options["ajaxContentType"] = {
            headers: {
                "Content-Type": "application/json",
                "X-Request-Generator": "tabulator"
            },
            body: function (url, config, params) {
                return JSON.stringify(params);
            },
        }
    }
}

export {initTabulator};
