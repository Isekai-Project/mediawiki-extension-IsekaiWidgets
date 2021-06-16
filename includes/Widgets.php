<?php
namespace Isekai\Widgets;

class Widgets {
    public static function onParserSetup(&$parser){
        $parser->setHook('createpage', CreatePageWidget::class . '::create');
        $parser->setHook('discoverbox', DiscoverWidget::class . '::create');
        
        $parser->setHook('tile', TileWidget::class . '::create');
        $parser->setHook('tilegroup', TileGroupWidget::class . '::create');
		return true;
    }
}