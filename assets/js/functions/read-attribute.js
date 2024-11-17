import _ from "lodash";

export function readAttribute(value) {
    const currentScript = document.querySelector("script[type='importmap']");

    if (currentScript !== null) {
        return currentScript.dataset[_.camelCase(value)];
    }

    return null;
}
