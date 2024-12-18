import "preline";
import "./plugins/dark-mode.js";

// Stylesheet
import "../styles/style.css";

// Import additional modules
import { initClipboard } from "./plugins/clipboard.js";
import { initConfirmationModal } from "./plugins/confirmation-modal.js";

// Init modules
initClipboard();
initConfirmationModal();
