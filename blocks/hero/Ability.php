<?php

namespace App\Blocks\Hero;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/hero-block';
    }

    public function label(): string
    {
        return 'Insert Hero Block';
    }

    public function description(): string
    {
        return 'Inserts a hero section with a selectable type: basic (no media), image background or self-hosted video background. Conditional fields per type: image needs background_image (attachment ID), video needs background_video (attachment ID, mp4/webm) and optional video_poster. overlay_opacity (0-90) darkens image/video for text contrast. height controls min-height (auto/medium/full screen). Use skeleton/upload-media-from-url first to get attachment IDs. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                'hero_type' => [
                    'type'        => 'string',
                    'enum'        => ['basic', 'image', 'video'],
                    'description' => 'Hero variant. Default: basic.',
                ],
                'title' => [
                    'type'        => 'string',
                    'description' => 'Main heading text.',
                ],
                'title_tag' => [
                    'type'        => 'string',
                    'enum'        => ['h1', 'h2'],
                    'description' => 'Heading tag. Use h2 if the page already has an h1. Default: h1.',
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
                'background_image' => [
                    'type'        => 'integer',
                    'description' => 'WP attachment ID. Required when hero_type is "image".',
                ],
                'background_video' => [
                    'type'        => 'integer',
                    'description' => 'WP attachment ID of a self-hosted mp4/webm. Required when hero_type is "video".',
                ],
                'video_poster' => [
                    'type'        => 'integer',
                    'description' => 'WP attachment ID of the poster image shown before the video loads.',
                ],
                'overlay_opacity' => [
                    'type'        => 'integer',
                    'description' => 'Dark overlay opacity over image/video, 0-90 in steps of 5. Default: 50.',
                ],
                'height' => [
                    'type'        => 'string',
                    'enum'        => ['auto', 'medium', 'full'],
                    'description' => 'Section min-height: auto (content), medium (~60vh), full (100vh). Default: medium.',
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
        $page = $this->resolvePost((int) ($input['post_id'] ?? 0));

        if (is_wp_error($page)) {
            return $page;
        }

        $align = $input['align'] ?? '';

        $block = $this->serializeBlock([
            'name'  => 'acf/hero',
            'align' => in_array($align, ['wide', 'full'], true) ? $align : '',
            'fields' => [
                'hero_type'        => $input['hero_type']        ?? 'basic',
                'title'            => $input['title']            ?? '',
                'title_tag'        => $input['title_tag']        ?? 'h1',
                'subtitle'         => $input['subtitle']         ?? '',
                'primary_button'   => $input['primary_button']   ?? null,
                'secondary_button' => $input['secondary_button'] ?? null,
                'background_image' => $input['background_image'] ?? null,
                'background_video' => $input['background_video'] ?? null,
                'video_poster'     => $input['video_poster']     ?? null,
                'overlay_opacity'  => $input['overlay_opacity']  ?? 50,
                'height'           => $input['height']           ?? 'medium',
            ],
        ]);

        $result = $this->insertBlockIntoPost($page->ID, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('hero-block', ['post_id' => $page->ID, 'after' => $input['after'] ?? null]);

        return $result;
    }
}
