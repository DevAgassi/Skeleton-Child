/**
 * Front-end entry — the project stylesheet and the project's own behaviour,
 * loaded on every page (manifest key resources/scripts/app.js).
 */

import "../css/app.css";
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