<?php
namespace Isekai\Widgets;

use Html;

class ExtraFontWidget {
    public static function create($text, $params, $parser, $frame){
        $existsFonts = $parser->extIsekaiWidgetsCache->get('extraFonts', INF, []);

        $content = $text = $frame->expand($text);
        if (!isset($params['name']) || empty($params['name'])) {
            return '<span class="error">' . wfMessage('isekai-font-error-invalid-params')->parse() . '</span>' . $content;
        }

        $fontName = 'extra-' . $params['name'];
        if (preg_match('/[`~!@#$%^&*()+=<>?:"{}|,.\/;\'\\\\\[\]]\r\n/', $fontName)) {
            return '<span class="error">' .
                wfMessage('isekai-font-error-font-name-invalid')->parse() .
                '</span>' .
                $content;
        }

        $existsFonts = $parser->extIsekaiWidgetsCache->get('extraFonts', INF, []);
        if (!isset($existsFonts[$fontName])) {
            return '<span class="error">' .
                wfMessage('isekai-font-error-font-not-imported', $params['name'])->parse() .
                '</span>' .
                $content;
        }
        $fontId = $existsFonts[$fontName];

        return [
            Html::rawElement('span', [
                'class' => 'isekai-extra-font font-' . $fontId,
            ], $content),
            "markerType" => 'nowiki'
        ];
    }
}