export const initConfirmationModal = () => {
    document.querySelectorAll("*[data-confirmation]").forEach(function (item) {

        // https://stackoverflow.com/a/19470348
        let cloneItem = item.cloneNode(true);
        item.parentElement.replaceChild(cloneItem, item);

        let onClick = (e) => {
            e.preventDefault();

            // const confirmationModal = HSOverlay.getInstance("#confirmation-modal", true);
            const confirmationModal = HSOverlay.getInstance("*[data-confirmation-modal]", true);
            confirmationModal.element.open();

            document.querySelectorAll("*[data-confirmation-confirm]").forEach(function (confirmButton) {
                confirmButton.addEventListener("click", function (confirmEvent) {
                    confirmEvent.preventDefault();

                    const confirmHref = item.href ?? item.dataset["confirmHref"];

                    if (confirmHref !== null && confirmHref !== undefined && confirmHref !== "") {
                        window.location = confirmHref;
                    }
                });
            });
        };

        cloneItem.addEventListener("click", onClick);
    });
}
