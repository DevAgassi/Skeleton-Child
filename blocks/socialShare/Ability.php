<?php

namespace App\Blocks\SocialShare;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/social-share-block';
    }

    public function label(): string
    {
        return 'Insert Social Share Block';
    }

    public function description(): string
    {
        return 'Inserts a social sharing block (X, Facebook, LinkedIn, Reddit) into a post or page. Requires post_id — the ID of the post or page to append the block to. The block renders share links dynamically using the post URL and title. Appends to existing content by default; use "after" (with skeleton/page-outline) for positional insert. Tip: the canonical placement is via Appearance → Widgets → "Single Post — Share Zone" so it appears automatically on all single posts.';
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
                    'description' => 'ID of the post or page to append the block to.',
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
        $post_id = (int) ($input['post_id'] ?? 0);

        if (!$post_id || !get_post($post_id)) {
            return new \WP_Error('not_found', "Post {$post_id} does not exist.");
        }

        if (!current_user_can('edit_post', $post_id)) {
            return new \WP_Error('forbidden', 'You do not have permission to edit this post.');
        }

        $block  = $this->serializeBlock(['name' => 'theme/social-share', 'fields' => []]);
        $result = $this->insertBlockIntoPost($post_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('social-share-block', ['post_id' => $post_id, 'after' => $input['after'] ?? null]);

        return $result;
    }
}
