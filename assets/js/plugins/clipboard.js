import ClipboardJS from "clipboard";

export const initClipboard = () => {
    const clipboard = new ClipboardJS("*[data-clipboard]");

    clipboard.on("success", function (event) {
        event.clearSelection();
    });
}
