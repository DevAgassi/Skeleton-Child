<?php

namespace App\Blocks\Tabs;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/tabs-block';
    }

    public function label(): string
    {
        return 'Insert Tabs Block';
    }

    public function description(): string
    {
        return 'Inserts a horizontal tabbed content block on a page. Each tab has a label (button text) and HTML content (WYSIWYG). Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                'tabs' => [
                    'type'        => 'array',
                    'description' => 'Ordered list of tabs.',
                    'items'       => [
                        'type'       => 'object',
                        'properties' => [
                            'label'   => ['type' => 'string', 'description' => 'Tab button text.'],
                            'content' => ['type' => 'string', 'description' => 'Tab panel content. HTML allowed.'],
                        ],
                        'required' => ['label', 'content'],
                    ],
                ],
                ...$this->positionParams(),
            ],
            'required' => ['post_id', 'tabs'],
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

    public function permissionCallback(): bool
    {
        return current_user_can('edit_posts');
    }

    public function execute(mixed $input): mixed
    {
        $post = $this->resolvePost((int) ($input['post_id'] ?? 0));
        if (is_wp_error($post)) {
            return $post;
        }

        $block = $this->serializeBlock([
            'name'   => 'acf/tabs',
            'fields' => [
                'tabs' => $input['tabs'] ?? [],
            ],
        ]);

        $result = $this->insertBlockIntoPost($post->ID, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('tabs-block', ['post_id' => $post->ID, 'tabs' => count($input['tabs'] ?? []), 'after' => $input['after'] ?? null]);

        return $result;
    }
}
