<?php
namespace Isekai\Widgets;

class FeedListWidget {
    public static function getHtml() {
        ob_start();
        include(dirname(__DIR__) . '/modules/feedList/ext.isekai.feedList.tpl');
        $template = ob_get_clean();
        return [$template, "markerType" => 'nowiki'];
    }
    
    public static function create($text, $params, \Parser $parser, \PPFrame $frame) {
        $parser->getOutput()->addModules(['ext.isekai.feedList']);
        
        return self::getHtml();
    }
}