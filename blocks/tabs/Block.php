<?php

namespace App\Blocks\Tabs;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'tabs';

    protected ?string $interactivity_namespace = 'tabs';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'tabs-block py-8 md:py-12'];
    }

    protected function setup(): void
    {
        $f    = $this->fields;
        $tabs = array_map(fn($tab, $i) => [
            'label'   => $tab['label'] ?? 'Tab ' . ($i + 1),
            'content' => $tab['content'] ?? '',
            'index'   => $i,
        ], $f['tabs'] ?? [], array_keys($f['tabs'] ?? []));

        $this->setInteractivityContext(['activeTab' => 0]);

        $this->context['block'] = [
            'title'    => $f['title'] ?? '',
            'subtitle' => $f['subtitle'] ?? '',
            'tabs'     => $tabs,
        ];
    }
}
