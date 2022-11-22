<?php
namespace Isekai\Widgets;

class CreatePageWidget {
    public static function getHtml(){
        ob_start();
        include(dirname(__DIR__) . '/modules/createPage/ext.isekai.createPage.tpl');
        $template = ob_get_clean();
        return [$template, "markerType" => 'nowiki'];
    }
    
    public static function create($text, $params, \Parser $parser, \PPFrame $frame){
        $parser->getOutput()->addModules(['ext.isekai.createPage']);

        return self::getHtml();
    }
}