<?php
namespace Isekai\Widgets;

class DiscoverWidget {
    public static function getHtml(){
        ob_start();
        include(dirname(__DIR__) . '/modules/discover/ext.isekai.discover.tpl');
        $template = ob_get_clean();
        return [$template, "markerType" => 'nowiki'];
    }
    
    public static function create($text, $params, \Parser $parser, $frame){
        $parser->getOutput()->addModules('ext.isekai.discover');
        
        return self::getHtml();
    }
}