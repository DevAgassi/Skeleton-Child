<?php

namespace App\Blocks\ContentSlider;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'contentSlider';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'content-slider-block py-8 md:py-12'];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $this->context['block'] = [
            'slides'   => array_map(static fn($s) => [
                'label'       => $s['label']       ?? '',
                'title'       => $s['title']        ?? '',
                'description' => $s['description']  ?? '',
                'link'        => $s['link']          ?? null,
                'button'      => $s['button']        ?? null,
                'image'       => $s['image']         ?? null,
            ], $f['slides'] ?? []),
            'autoplay'        => !empty($f['autoplay']),
            'loop'            => !isset($f['loop']) || (bool) $f['loop'],
            'show_copyright'  => !empty($f['show_copyright']),
            'show_sandbox'    => !empty($f['show_sandbox']),
        ];
    }
}
