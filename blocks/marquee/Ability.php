<?php

namespace App\Blocks\Marquee;

use App\Core\Abilities\ContentAbility;

class Ability extends ContentAbility
{
    public function name(): string
    {
        return 'skeleton/marquee-block';
    }

    public function label(): string
    {
        return 'Insert Marquee Logos Block';
    }

    public function description(): string
    {
        return 'Inserts an infinite-scrolling logo strip on a page. Each logo references an SVG filename from the static/brands/ directory. Available filenames: Mastercard.svg, Skrill.svg, Stripe.svg, Visa.svg, bet360.svg, bet365.svg, bitpay.svg, bloomberg.svg, cash_app.svg, casumo.svg, databrocks.svg, discord.svg, dropbox.svg, entain.svg, github.svg, google.svg, hotjar.svg, kaizen.svg, klarna.svg, lottoland.svg, millionz.svg, opensea.svg, pampa_go.svg, paypal.svg, sg.svg, sisal.svg, slack.svg, spotify.svg, stake.svg, twitch.svg, uber.svg, webflow.svg. Each logo can have an optional URL link. Appends to existing page content by default; use "after" (with skeleton/page-outline) for positional insert.';
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
                'logos' => [
                    'type'        => 'array',
                    'description' => 'Ordered list of logos to display in the marquee.',
                    'items'       => [
                        'type'       => 'object',
                        'properties' => [
                            'logo' => [
                                'type'        => 'string',
                                'description' => 'SVG filename from static/brands/, e.g. "google.svg" or "paypal.svg".',
                            ],
                            'link' => [
                                'type'        => 'string',
                                'description' => 'Optional URL to wrap the logo in a link.',
                            ],
                        ],
                        'required' => ['logo'],
                    ],
                ],
                ...$this->positionParams(),
            ],
            'required' => ['post_id', 'logos'],
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
            'name'   => 'acf/marquee',
            'fields' => [
                'logos' => $input['logos'] ?? [],
            ],
        ]);

        $result = $this->insertBlockIntoPost($page_id, $block, $input);

        if (is_wp_error($result)) {
            return $result;
        }

        $this->auditLog('marquee-block', ['post_id' => $page_id, 'logos' => count($input['logos'] ?? []), 'after' => $input['after'] ?? null]);

        return $result;
    }
}
