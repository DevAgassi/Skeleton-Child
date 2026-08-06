<?php

namespace App\Blocks\Marquee;

use App\Core\Blocks\AcfBlock;

class Block extends AcfBlock
{
    public string $blockName = 'marquee';

    /** Brand logos bundled with the project. */
    private const BRANDS_DIR = '/static/brands/';

    protected function defaultWrapperClasses(): array
    {
        return ['class' => 'marquee-block py-8 md:py-12'];
    }

    protected function setup(): void
    {
        $f = $this->fields;

        $this->context['block'] = [
            'logos' => array_values(array_filter(array_map(
                fn(array $item): array => $this->resolveLogo($item),
                $f['logos'] ?? []
            ))),
        ];
    }

    /**
     * Resolve one repeater row to a renderable logo.
     *
     * A row carries either a bundled brand filename or an uploaded image; the
     * upload wins when both are set. Rows with neither are dropped rather than
     * rendered as an empty <img>.
     *
     * @param  array $item Repeater row: logo (string), image (array), link (string).
     * @return array{src: string, alt: string, link: string}|array{}
     */
    private function resolveLogo(array $item): array
    {
        $link  = $item['link'] ?? '';
        $image = $item['image'] ?? null;

        if (is_array($image) && !empty($image['url'])) {
            return [
                'src'  => $image['url'],
                'alt'  => $image['alt'] ?: ($image['title'] ?? ''),
                'link' => $link,
            ];
        }

        $file = $item['logo'] ?? '';

        if ($file === '') {
            return [];
        }

        return [
            'src'  => get_stylesheet_directory_uri() . self::BRANDS_DIR . $file,
            'alt'  => ucwords(str_replace(['-', '_', '.svg'], [' ', ' ', ''], $file)),
            'link' => $link,
        ];
    }

    public static function registerHooks(): void
    {
        // Populate the brand picker from the files actually present on disk,
        // so adding an SVG to static/brands/ is the whole workflow.
        add_filter('acf/load_field/key=field_marquee_logo_select', static function (array $field): array {
            $files = glob(get_stylesheet_directory() . self::BRANDS_DIR . '*.svg') ?: [];
            sort($files);

            $field['choices'] = [];

            foreach ($files as $file) {
                $filename = basename($file);
                $field['choices'][$filename] = ucwords(str_replace(
                    ['-', '_', '.svg'],
                    [' ', ' ', ''],
                    pathinfo($filename, PATHINFO_FILENAME)
                ));
            }

            return $field;
        });
    }
}
