import { stripHtml } from "./dom-utils";
import { formatDate } from "./date-utils";

/**
 * Transforms a WP REST API post object into a simpler format for our app.
 * @param post 
 * @returns Object
 */
export function mapPost(post) {
  const featured = post._embedded?.["wp:featuredmedia"]?.[0] ?? null;
  return {
    id: post.id,
    title: post.title?.rendered || "",
    link: post.link,
    imageUrl: featured?.media_details?.sizes?.medium?.source_url || null,
    excerpt: stripHtml(post.excerpt?.rendered || ""),
    date: formatDate(post.date),
  };
}
