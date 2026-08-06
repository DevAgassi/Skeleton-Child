<?php

namespace App\Blocks\TextWithImage;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'textWithImage';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'twi-block py-8'];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $image = $f['image'] ?? null;

        $this->context['block'] = [
            'label'       => $f['label'] ?? '',
            'title'       => $f['title'] ?? '',
            'description' => $f['description'] ?? '',
            'cta'         => $f['cta'] ?? null,
            'image'       => $image,
        ];

        $raw_mobile  = $f['image_aspect_mobile'] ?? 'auto';
        $raw_desktop = $f['image_aspect'] ?? 'auto';

        // CSS custom properties injected via style attr — avoids Tailwind dynamic class scanning issue.
        // index.css handles the responsive switch at 1024px using these vars.
        $vars = [];
        if ($raw_mobile  !== 'auto') $vars[] = '--twi-aspect-m:' . $raw_mobile  . ';--twi-img-maxh-m:none';
        if ($raw_desktop !== 'auto') $vars[] = '--twi-aspect-d:' . $raw_desktop . ';--twi-img-maxh-d:none';

        $this->context['has_image']     = !empty($image);
        $this->context['image_left']    = ($f['image_position'] ?? 'right') === 'left';
        $this->context['is_centered']   = ($f['text_align'] ?? 'left') === 'center';
        $this->context['twi_style']     = $vars ? implode(';', $vars) . ';' : '';
        $this->context['show_copyright'] = !isset($f['show_copyright']) || (bool) $f['show_copyright'];
        $this->context['show_sandbox']   = !isset($f['show_sandbox'])   || (bool) $f['show_sandbox'];
    }
}
