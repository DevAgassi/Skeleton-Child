<?php

namespace App\Blocks\Accordion;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/accordion-block';
    }

    public function label(): string
    {
        return 'Insert Accordion Block';
    }

    public function description(): string
    {
        return 'Inserts an accordion block (expandable FAQ-style list) on a page. Provide an optional section title and an ordered list of items, each with a label (question) and HTML description (answer). Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                    'description' => 'ID of the page to append the block to.',
                ],
                'title' => [
                    'type'        => 'string',
                    'description' => 'Optional section heading displayed above the accordion.',
                ],
                'items' => [
                    'type'        => 'array',
                    'description' => 'Ordered list of accordion items.',
                    'items'       => [
                        'type'       => 'object',
                        'properties' => [
                            'label'       => ['type' => 'string', 'description' => 'Item heading / question text.'],
                            'description' => ['type' => 'string', 'description' => 'Item body / answer. HTML allowed.'],
                        ],
                        'required' => ['label', 'description'],
                    ],
                ],
                ...$this->positionParams(),
            ],
            'required' => ['post_id', 'items'],
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
            'name'   => 'acf/accordion',
            'fields' => [
                'title' => $input['title'] ?? '',
                'items' => $input['items'] ?? [],
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('accordion-block', ['post_id' => $page_id, 'items' => count($input['items'] ?? []), 'after' => $input['after'] ?? null]);

        return $result;
    }
}
