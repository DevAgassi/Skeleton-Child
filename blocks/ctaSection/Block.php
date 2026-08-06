<?php

namespace App\Blocks\CtaSection;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'ctaSection';

    protected bool $fullBleed = true;

    protected function defaultWrapperClasses(): array
    {
        $variant = $this->fields['variant'] ?? 'dark';

        return ['class' => "cta-section-block cta-section--{$variant} py-8 md:py-12"];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $this->context['block'] = [
            'title'            => $f['title'] ?? '',
            'subtitle'         => $f['subtitle'] ?? '',
            'primary_button'   => $f['primary_button'] ?? null,
            'secondary_button' => $f['secondary_button'] ?? null,
            'image'            => $f['background_image'] ?? null,
        ];
    }
}
