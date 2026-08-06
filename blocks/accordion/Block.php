<?php

namespace App\Blocks\Accordion;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'accordion';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'accordion-block py-8'];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $this->context['block'] = [
            'title' => $f['title'] ?? '',
            'items' => $f['items'] ?? [],
        ];
    }
}
