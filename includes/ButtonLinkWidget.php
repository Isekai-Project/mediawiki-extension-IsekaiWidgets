<?php
namespace Isekai\Widgets;

use Title;
use Html;

class ButtonLinkWidget {
    /**
     * @param string $text
     * @param array $params
     * @param \Parser $parser
     * @param \PPFrame $frame
     * @return string|string[]
     */
    public static function create($text, $params, \Parser $parser, \PPFrame $frame) {
        $out = $parser->getOutput();
        $out->addModules([
            "ext.isekai.buttonLink"
        ]);

        if (isset($params['page'])) {
            $title = Title::newFromText($params['page']);
            if ($title) {
                $params['href'] = $title->getFullURL();
            }
        }

        $framed = true;
        if (isset($params['frameless']) && $params['frameless']) {
            $framed = false;
        }

        $flags = [];

        $primary = true;
        $type = 'progressive';
        if (isset($params['default']) && $params['default']) {
            $primary = false;
            $type = null;
        }
        if (isset($params['secondary']) && $params['secondary']) {
            $primary = false;
        }
        if (isset($params['destructive']) && $params['destructive']) {
            $flags[] = 'destructive';
        }
        if ($primary) {
            $flags[] = 'primary';
        }
        if ($type) {
            $flags[] = $type;
        }

        $flags = implode(' ', $flags);

        $html = Html::element('a', [
            'class' => 'isekai-buttonlink',
            'href' => $params['href'] ?? '#',
            'target' => $params['target'] ?? '_self',
            'data-framed' => $framed ? 'true' : 'false',
            'data-flags' => $flags
        ], $text);

        return [$html, "markerType" => 'nowiki'];
    }
}