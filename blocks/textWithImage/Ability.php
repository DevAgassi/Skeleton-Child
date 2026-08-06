<?php

namespace App\Blocks\TextWithImage;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/text-with-image-block';
    }

    public function label(): string
    {
        return 'Insert Text with Image Block';
    }

    public function description(): string
    {
        return 'Inserts a two-column text+image block on a page. Columns stack on mobile. image_position controls which side the image appears on. text_align controls heading/text alignment. image_aspect and image_aspect_mobile set the image crop ratio. show_copyright and show_sandbox are optional toggles. cta is an optional link object with url, title, target. image is a WP attachment ID — use skeleton/upload-media-from-url first if you need to upload one. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                'label' => [
                    'type'        => 'string',
                    'description' => 'Optional eyebrow label above the title.',
                ],
                'title' => [
                    'type'        => 'string',
                    'description' => 'Optional main heading.',
                ],
                'description' => [
                    'type'        => 'string',
                    'description' => 'Body text. HTML allowed.',
                ],
                'cta' => [
                    'type'        => 'object',
                    'description' => 'Optional call-to-action link.',
                    'properties'  => [
                        'url'    => ['type' => 'string'],
                        'title'  => ['type' => 'string'],
                        'target' => ['type' => 'string', 'description' => '"_blank" to open in new tab.'],
                    ],
                ],
                'text_align' => [
                    'type'        => 'string',
                    'enum'        => ['left', 'center'],
                    'description' => 'Text alignment. Default: left.',
                ],
                'image_position' => [
                    'type'        => 'string',
                    'enum'        => ['right', 'left'],
                    'description' => 'Side where the image appears. Default: right.',
                ],
                'image_aspect' => [
                    'type'        => 'string',
                    'enum'        => ['auto', '16/9', '4/3', '1/1', '5/3'],
                    'description' => 'Image crop ratio on desktop. Default: auto.',
                ],
                'image_aspect_mobile' => [
                    'type'        => 'string',
                    'enum'        => ['auto', '4/3', '1/1'],
                    'description' => 'Image crop ratio on mobile. Default: auto.',
                ],
                'image' => [
                    'type'        => 'integer',
                    'description' => 'WP attachment ID. Use skeleton/upload-media-from-url to get one.',
                ],
                'show_copyright' => [
                    'type'        => 'boolean',
                    'description' => 'Show copyright badge on image. Default: false.',
                ],
                'show_sandbox' => [
                    'type'        => 'boolean',
                    'description' => 'Enable fullscreen image preview. Default: false.',
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
            'name'   => 'acf/text-with-image',
            'fields' => [
                'label'               => $input['label']               ?? '',
                'title'               => $input['title']               ?? '',
                'description'         => $input['description']         ?? '',
                'cta'                 => $input['cta']                 ?? null,
                'text_align'          => $input['text_align']          ?? 'left',
                'image_position'      => $input['image_position']      ?? 'right',
                'image_aspect'        => $input['image_aspect']        ?? 'auto',
                'image_aspect_mobile' => $input['image_aspect_mobile'] ?? 'auto',
                'image'               => $input['image']               ?? null,
                'show_copyright'      => $input['show_copyright']      ?? false,
                'show_sandbox'        => $input['show_sandbox']        ?? false,
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('text-with-image-block', ['post_id' => $page_id, 'after' => $input['after'] ?? null]);

        return $result;
    }
}
