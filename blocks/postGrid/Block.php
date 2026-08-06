<?php

namespace App\Blocks\PostGrid;

use App\Core\Blocks\AcfBlock;
use App\Models\Post\Post;
use Timber\Timber;

class Block extends AcfBlock
{
    public string $blockName = 'postGrid';

    protected ?string $interactivity_namespace = 'postGrid';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'post-grid-block py-8 md:py-12'];
    }

    protected function setup(): void
    {
        $f              = $this->fields;
        $selection_type = $f['selection_type'] ?? 'manual';
        $taxonomy_type  = $f['taxonomy_type']  ?? 'category';

        ['posts' => $posts, 'filters' => $filters, 'has_filters' => $has_filters] =
            $selection_type === 'taxonomy'
                ? $this->resolveTaxonomy($taxonomy_type)
                : $this->resolveManual();

        $this->setInteractivityContext(['activeFilter' => 'all']);

        $this->context['block'] = [
            'title'          => $f['title']          ?? '',
            'card_style'     => $f['card_style']     ?? 'standard',
            'selection_type' => $selection_type,
            'posts'          => $posts,
            'has_filters'    => $has_filters,
            'filters'        => $filters,
            'explore_link'   => !empty($f['explore_link']) ? $f['explore_link'] : null,
        ];
    }

    private function resolveManual(): array
    {
        $posts = array_map(
            fn($raw) => ['post' => Timber::get_post($raw), 'terms' => []],
            is_array($this->fields['posts'] ?? null) ? $this->fields['posts'] : []
        );

        return ['posts' => $posts, 'filters' => [], 'has_filters' => false];
    }

    private function resolveTaxonomy(string $taxonomy_type): array
    {
        $taxonomy = $taxonomy_type === 'category' ? 'category' : 'post_tag';
        $raw_terms = $taxonomy_type === 'category'
            ? ($this->fields['category_terms'] ?? null)
            : ($this->fields['tag_terms']      ?? null);
        $terms    = is_array($raw_terms) ? $raw_terms : [];
        $term_ids = array_map(fn(\WP_Term $t) => $t->term_id, $terms);

        if (empty($term_ids)) {
            return ['posts' => [], 'filters' => [], 'has_filters' => false];
        }

        $timber_posts = Post::queryByTaxonomy($taxonomy, $term_ids, (int) ($this->fields['max_posts'] ?? 12));
        $has_filters  = count($term_ids) > 1;
        $map          = $has_filters ? Post::termsMap($timber_posts, $taxonomy, $term_ids) : [];

        $counts = ['all' => count($timber_posts)];
        foreach ($map as $slugs) {
            foreach ($slugs as $slug) {
                $counts[$slug] = ($counts[$slug] ?? 0) + 1;
            }
        }

        $filters = array_map(
            fn($f) => [...$f, 'count' => $counts[$f['slug']] ?? 0],
            $has_filters ? Post::termFilters($taxonomy, $term_ids) : []
        );

        return [
            'posts'       => array_map(fn($p) => ['post' => $p, 'terms' => $map[$p->ID] ?? []], $timber_posts),
            'filters'     => $filters,
            'has_filters' => $has_filters,
        ];
    }

}
