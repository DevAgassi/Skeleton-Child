<?php

namespace App\Blocks\CtaSection;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/cta-section-block';
    }

    public function label(): string
    {
        return 'Insert CTA Section Block';
    }

    public function description(): string
    {
        return 'Inserts a call-to-action section with heading, subtitle, up to two buttons, style variant and optional background image. primary_button and secondary_button are link objects with url, title, optional target. variant controls the color scheme: dark (default), light, or primary. background_image is a WP attachment ID — use skeleton/upload-media-from-url first if needed. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                    'description' => 'Main heading text.',
                ],
                'subtitle' => [
                    'type'        => 'string',
                    'description' => 'Optional subtitle below the heading.',
                ],
                'primary_button' => [
                    'type'        => 'object',
                    'description' => 'Primary CTA button.',
                    'properties'  => [
                        'url'    => ['type' => 'string'],
                        'title'  => ['type' => 'string'],
                        'target' => ['type' => 'string', 'description' => '"_blank" to open in new tab.'],
                    ],
                ],
                'secondary_button' => [
                    'type'        => 'object',
                    'description' => 'Optional secondary button.',
                    'properties'  => [
                        'url'    => ['type' => 'string'],
                        'title'  => ['type' => 'string'],
                        'target' => ['type' => 'string'],
                    ],
                ],
                'variant' => [
                    'type'        => 'string',
                    'enum'        => ['dark', 'light', 'primary'],
                    'description' => 'Color scheme. Default: dark.',
                ],
                'background_image' => [
                    'type'        => 'integer',
                    'description' => 'WP attachment ID for background image. Use skeleton/upload-media-from-url to get one.',
                ],
                'align' => [
                    'type'        => 'string',
                    'enum'        => ['wide', 'full'],
                    'description' => 'Content container width: wide = 1280px, full = edge to edge. Omit for the default 1180px.',
                ],
                ...$this->positionParams(),
            ],
            'required' => ['post_id', 'title'],
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

        $align = $input['align'] ?? '';

        $block = $this->serializeBlock([
            'name'  => 'acf/cta-section',
            'align' => in_array($align, ['wide', 'full'], true) ? $align : '',
            'fields' => [
                'title'            => $input['title']            ?? '',
                'subtitle'         => $input['subtitle']         ?? '',
                'primary_button'   => $input['primary_button']   ?? null,
                'secondary_button' => $input['secondary_button'] ?? null,
                'variant'          => $input['variant']          ?? 'dark',
                'background_image' => $input['background_image'] ?? null,
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('cta-section-block', ['post_id' => $page_id, 'after' => $input['after'] ?? null]);

        return $result;
    }
}
