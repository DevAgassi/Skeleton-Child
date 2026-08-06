/**
 * Front-end entry — the project stylesheet, the engine's boot sequence and the
 * project's own behaviour.
 *
 * Shadows the engine's entry of the same relative path, so the child build
 * emits this one under the manifest key resources/scripts/app.js.
 */

import "../css/app.css";
import "../../../Skeleton/resources/scripts/boot";
import { initSandbox } from "./sandbox";
import { initStickyHeader, initMobileMenu, initScrollToTop } from "./nav";
import { getAppData } from "@shared";

document.addEventListener("DOMContentLoaded", () => {
	initStickyHeader();
	initMobileMenu();
	initScrollToTop();
	initSandbox();
});


console.log("✅ Theme App.js", { ...getAppData() });