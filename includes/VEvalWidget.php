<?php
namespace Isekai\Widgets;

class VEvalWidget {
    public static function create($text, $params, \Parser $parser, \PPFrame $frame) {
        $content = $text = $parser->recursiveTagParse($text, $frame);
        return [$content, "markerType" => 'nowiki'];
    }
}