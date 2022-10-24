<?php
namespace Isekai\Widgets;

use Html;

class Html5Widget {
    public static function createDetails(string $text, array $args, \Parser $parser, \PPFrame $frame) {
        $parser->getOutput()->addModules('ext.isekai.collapse');
        $allowedAttr = ['class'];
        $htmlArgs = array_filter($args, function($k) use($allowedAttr) {
            return in_array($k, $allowedAttr);
        }, ARRAY_FILTER_USE_KEY);

        $content = '';
        if ($text) {
            $content = Utils::makeParagraph($parser->recursiveTagParse($text, $frame), true);
        }

        return [Html::rawElement('details', $htmlArgs, $content), "markerType" => 'nowiki'];
    }

    public static function createSummary(string $text, array $args, \Parser $parser, \PPFrame $frame) {
        $allowedAttr = ['class'];
        $htmlArgs = array_filter($args, function($k) use($allowedAttr) {
            return in_array($k, $allowedAttr);
        }, ARRAY_FILTER_USE_KEY);

        $content = '';
        if ($text) {
            $content = $parser->recursiveTagParse($text, $frame);
        }

        return [Html::rawElement('summary', $htmlArgs, $content), "markerType" => 'nowiki'];
    }
}