import { TabulatorFull as Tabulator } from "tabulator-tables";
import { readAttribute } from "../functions/read-attribute.js";
import { initTabulator } from "../../../vendor/m-adamski/symfony-tabulator-bundle/src/Resources/public/js/tabulator.js";

// Modules
import { initClipboard } from "../plugins/clipboard.js";
import { initConfirmationModal } from "../plugins/confirmation-modal.js";

let tableConfigAttr = readAttribute("table-config");

if (null !== tableConfigAttr) {
    let tableConfig = JSON.parse(tableConfigAttr);

    // Init Tabulator
    initTabulator(Tabulator, tableConfig).then((tabulator) => {
        let searchInput = document.querySelector("input[data-table-search]");
        let searchConfig = searchInput.dataset["tableSearch"];

        searchInput.addEventListener("input", (event) => {
            let searchValue = event.target.value;

            if (searchValue.length > 0) {
                let currentSearchConfig = searchConfig.replaceAll("%", searchValue);

                tabulator.setFilter(
                    JSON.parse(currentSearchConfig)
                );
            } else {
                tabulator.clearFilter();
            }
        });

        // Reinit
        let renderTimeout = null;

        tabulator.on("renderComplete", () => {
            clearTimeout(renderTimeout);

            renderTimeout = setTimeout(() => {
                window.HSStaticMethods.autoInit();
                initClipboard();
                initConfirmationModal();
            }, 500);
        });
    });
}
