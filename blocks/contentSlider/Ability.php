<?php

namespace App\Blocks\ContentSlider;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/content-slider';
    }

    public function label(): string
    {
        return 'Insert Content Slider Block';
    }

    public function description(): string
    {
        return 'Inserts a content slider block on a page. Each slide has an image, required description, and optional label, title, text link, and CTA button. Use skeleton/list-blocks first to confirm field keys. Supports autoplay, loop, copyright badge, and image sandbox preview toggles. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                'slides' => [
                    'type'        => 'array',
                    'description' => 'Ordered list of slides.',
                    'items'       => [
                        'type'       => 'object',
                        'properties' => [
                            'image'       => ['type' => 'integer', 'description' => 'Attachment ID of the slide image.'],
                            'description' => ['type' => 'string',  'description' => 'Required. Main slide body text (HTML allowed).'],
                            'label'       => ['type' => 'string',  'description' => 'Optional eyebrow label above the title.'],
                            'title'       => ['type' => 'string',  'description' => 'Optional slide heading.'],
                            'link'        => ['type' => 'object',  'description' => 'Optional text link. Keys: url, title, target.'],
                            'button'      => ['type' => 'object',  'description' => 'Optional CTA button. Keys: url, title, target.'],
                        ],
                        'required' => ['image', 'description'],
                    ],
                ],
                'autoplay'       => ['type' => 'boolean', 'description' => 'Auto-advance slides every 5 seconds. Default false.'],
                'loop'           => ['type' => 'boolean', 'description' => 'Loop from last slide to first. Default true.'],
                'show_copyright' => ['type' => 'boolean', 'description' => 'Show copyright badge on images. Default false.'],
                'show_sandbox'   => ['type' => 'boolean', 'description' => 'Enable fullscreen image preview. Default false.'],
                ...$this->positionParams(),
            ],
            'required' => ['post_id', 'slides'],
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
            'name'   => 'acf/content-slider',
            'fields' => [
                'slides'         => $input['slides'] ?? [],
                'autoplay'       => $input['autoplay']       ?? false,
                'loop'           => $input['loop']           ?? true,
                'show_copyright' => $input['show_copyright'] ?? false,
                'show_sandbox'   => $input['show_sandbox']   ?? false,
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('content-slider', ['post_id' => $page_id, 'slides' => count($input['slides'] ?? []), 'after' => $input['after'] ?? null]);

        return $result;
    }
}
