<?php
namespace Isekai\Widgets;

class CreatePageWidget {
    public static function getHtml(){
        ob_start();
        include(dirname(__DIR__) . '/modules/createPage/ext.isekai.createPageWidget.tpl');
        $template = ob_get_clean();
        return [$template, "markerType" => 'nowiki'];
    }
    
    public static function create($text, $params, $parser, $frame){
        $parser->getOutput()->addModules('ext.isekai.createPage');

        return self::getHtml();
    }
}