/**
 * Очистка HTML тегов
 */
export function stripHtml(html) {
  if (!html) return "";
  if (typeof document === "undefined") return html.replace(/<[^>]*>?/gm, "");
  const div = document.createElement("div");
  div.innerHTML = html;
  return div.textContent || div.innerText || "";
}
