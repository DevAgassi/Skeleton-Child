<?php

namespace App\Blocks\PostGrid;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/post-grid';
    }

    public function label(): string
    {
        return 'Insert Post Grid Block';
    }

    public function description(): string
    {
        return 'Inserts a post grid block on a page. Supports two selection modes: manual (hand-picked posts via IDs) or taxonomy (category/tag). Multiple terms enable filter tabs. Optional explore link appears top-right. Use skeleton/list-blocks to confirm field keys. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
    }

    public function category(): string
    {
        return 'content';
    }

    public function permissionCallback(): bool
    {
        return current_user_can('edit_posts');
    }

    public function inputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'post_id' => [
                    'type'        => 'integer',
                    'description' => 'ID of the page to insert the block into.',
                ],
                'title' => [
                    'type'        => 'string',
                    'description' => 'Optional section heading displayed top-left.',
                ],
                'card_style' => [
                    'type'        => 'string',
                    'enum'        => ['standard', 'overlay'],
                    'description' => 'standard: image top + text below. overlay: image fills card, text on hover. Default: standard.',
                ],
                'selection_type' => [
                    'type'        => 'string',
                    'enum'        => ['manual', 'taxonomy'],
                    'description' => 'manual: provide post IDs. taxonomy: provide term IDs. Default: manual.',
                ],
                'posts' => [
                    'type'        => 'array',
                    'description' => 'Required when selection_type is manual. Array of post IDs.',
                    'items'       => ['type' => 'integer'],
                ],
                'taxonomy_type' => [
                    'type'        => 'string',
                    'enum'        => ['category', 'tag'],
                    'description' => 'Required when selection_type is taxonomy.',
                ],
                'category_terms' => [
                    'type'        => 'array',
                    'description' => 'Term IDs when taxonomy_type is category. Multiple terms add filter tabs.',
                    'items'       => ['type' => 'integer'],
                ],
                'tag_terms' => [
                    'type'        => 'array',
                    'description' => 'Term IDs when taxonomy_type is tag. Multiple terms add filter tabs.',
                    'items'       => ['type' => 'integer'],
                ],
                'max_posts' => [
                    'type'        => 'integer',
                    'description' => 'Max posts to fetch in taxonomy mode. Default: 12.',
                ],
                'explore_link' => [
                    'type'        => 'object',
                    'description' => 'Optional button top-right. Leave empty to hide.',
                    'properties'  => [
                        'url'    => ['type' => 'string'],
                        'title'  => ['type' => 'string'],
                        'target' => ['type' => 'string'],
                    ],
                ],
                ...$this->positionParams(),
            ],
            'required' => ['post_id'],
        ];
    }

    public function outputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'id'       => ['type' => 'integer'],
                'url'      => ['type' => 'string'],
                'modified' => ['type' => 'string'],
                'outline'  => ['type' => 'array', 'description' => 'Updated page outline — verify placement here.'],
            ],
        ];
    }

    public function execute(mixed $input): mixed
    {
        $page_id = (int) ($input['post_id'] ?? 0);

        if (!$page_id || !get_post($page_id)) {
            return new \WP_Error('not_found', "Page {$page_id} does not exist.");
        }

        if (!current_user_can('edit_post', $page_id)) {
            return new \WP_Error('forbidden', 'You do not have permission to edit this page.');
        }

        $block = $this->serializeBlock([
            'name'   => 'acf/post-grid',
            'fields' => [
                'title'          => $input['title']          ?? '',
                'card_style'     => $input['card_style']     ?? 'standard',
                'selection_type' => $input['selection_type'] ?? 'manual',
                'taxonomy_type'  => $input['taxonomy_type']  ?? 'category',
                'posts'          => $input['posts']          ?? [],
                'category_terms' => $input['category_terms'] ?? [],
                'tag_terms'      => $input['tag_terms']      ?? [],
                'max_posts'      => $input['max_posts']      ?? 12,
                'explore_link'   => $input['explore_link']   ?? null,
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('post-grid', ['post_id' => $page_id, 'selection_type' => $input['selection_type'] ?? 'manual', 'after' => $input['after'] ?? null]);

        return $result;
    }
}
