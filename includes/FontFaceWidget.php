<?php
namespace Isekai\Widgets;

use Title;
use MediaWiki\MediaWikiServices;

class FontFaceWidget {
    /**
     * @param string $text
     * @param array $params
     * @param \Parser $parser
     * @param \PPFrame $frame
     * @return string|string[]
     */
    public static function create($text, $params, \Parser $parser, \PPFrame $frame) {
        if (empty($params['src']) || empty($params['name'])) {
            return '<span class="error">' . wfMessage('isekai-fontface-error-invalid-params')->parse() . '</span>';
        }

        $service = MediaWikiServices::getInstance();

        $fontName = 'extra-' . $params['name'];
        $existsFonts = $parser->extIsekaiWidgetsCache->get('extraFonts', INF, []);
        if (isset($existsFonts[$fontName])) {
            return '<span class="error">' .
                wfMessage('isekai-fontface-error-font-already-defined', $params['name'])->parse() .
                '</span>';
        }
        if (preg_match('/[`~!@#$%^&*()+=<>?:"{}|,.\/;\'\\\\\[\]]\r\n/', $fontName)) {
            return '<span class="error">' .
                wfMessage('isekai-fontface-error-font-name-invalid')->parse() .
                '</span>';
        }

        $title = Title::newFromText($params['src'], NS_FILE);
        $file = $service->getRepoGroup()->findFile($title);
        if (!$file) {
            return '<span class="error">' .
                wfMessage('isekai-fontface-error-font-not-exists', $params['src'])->parse() .
                '</span>';
        }

        $fontUrl = $file->getUrl();
        $fontId = substr(Utils::safeBase64Encode(md5($fontName, true)), 0, 8);
        $css = "<span><style>@font-face{src: url('{$fontUrl}');font-family:'{$fontName}'}" .
            ".isekai-extra-font.font-{$fontId}{font-family:'{$fontName}'}</style></span>";

        $existsFonts[$fontName] = $fontId;
        $existsFonts = $parser->extIsekaiWidgetsCache->set('extraFonts', $existsFonts);

        return [$css, "markerType" => 'nowiki'];
    }
}