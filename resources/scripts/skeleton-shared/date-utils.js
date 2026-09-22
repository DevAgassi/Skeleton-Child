/**
 * Форматирование даты в строку вида "1 січня 2020"
 * @param {string} dateString - Дата в формате ISO (например, "2020-01-01T00:00:00")
 * @param {string} [locale="uk-UA"] - Локаль для форматирования (по умолчанию "uk-UA")
 * @returns {string} Отформатированная дата
 */
export function formatDate(dateString, locale = "uk-UA") {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString(locale, {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}
