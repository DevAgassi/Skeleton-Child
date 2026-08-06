<?php

namespace App\Blocks\Hero;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    /** Whitelist for the dynamic partial include in view.twig. */
    private const TYPES = ['basic', 'image', 'video'];

    public string $blockName = 'hero';

    protected bool $fullBleed = true;

    protected function defaultWrapperClasses(): array
    {
        $f = $this->fields;

        $height = $f['height'] ?? 'medium';

        return ['class' => "hero-block hero--{$this->type()} hero--h-{$height}"];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $this->context['block'] = [
            'type'             => $this->type(),
            'title'            => $f['title'] ?? '',
            'title_tag'        => $f['title_tag'] ?? 'h1',
            'subtitle'         => $f['subtitle'] ?? '',
            'primary_button'   => $f['primary_button'] ?? null,
            'secondary_button' => $f['secondary_button'] ?? null,
        ];

        match ($this->type()) {
            'image' => $this->setupImage(),
            'video' => $this->setupVideo(),
            default => null,
        };
    }

    private function type(): string
    {
        $type = $this->fields['hero_type'] ?? 'basic';

        return in_array($type, self::TYPES, true) ? $type : 'basic';
    }

    private function setupImage(): void
    {
        $this->context['block']['image'] = $this->fields['background_image'] ?? null;
        $this->setupOverlay();
    }

    private function setupVideo(): void
    {
        $this->context['block']['video']  = $this->fields['background_video'] ?? null;
        $this->context['block']['poster'] = $this->fields['video_poster'] ?? null;
        $this->setupOverlay();
    }

    private function setupOverlay(): void
    {
        $opacity = (int) ($this->fields['overlay_opacity'] ?? 50);

        // CSS custom property via style attr — same pattern as textWithImage aspect ratio.
        $this->context['hero_style'] = '--hero-overlay:' . ($opacity / 100) . ';';
    }
}
