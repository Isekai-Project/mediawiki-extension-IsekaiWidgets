<?php
namespace Isekai\Widgets;

class PreviewCardWidget {
    public static function getHtml($variables){
        extract($variables);
        ob_start();
        include(dirname(__DIR__) . '/modules/previewCard/ext.isekai.previewCard.html');
        $template = ob_get_clean();
        return [$template, "markerType" => 'nowiki'];
    }
    
    public static function create($text, $params, $parser, $frame){
        $parser->getOutput()->addModules('ext.isekai.previewCard');
        
        $titleChunk = explode('/', $text);
        $len = count($titleChunk);
        $displayTitle = $titleChunk[$len - 1];
        unset($titleChunk[$len - 1]);
        $path = implode('/', $titleChunk);
        return self::getHtml([
            'title' => $text,
            'displayTitle' => $displayTitle,
            'path' => $path,
        ]);
    }
}