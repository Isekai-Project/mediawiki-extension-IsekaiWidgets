<?php
namespace Isekai\Widgets;

use MapCacheLRU;

class Widgets {
    public static function onParserSetup(&$parser){
		$parser->extIsekaiWidgetsCache = new MapCacheLRU( 100 ); // 100 is arbitrary

        $parser->setHook('createpage', CreatePageWidget::class . '::create');
        $parser->setHook('discoverbox', DiscoverWidget::class . '::create');
        $parser->setHook('previewcard', PreviewCardWidget::class . '::create');
        
        $parser->setHook('tile', TileWidget::class . '::create');
        $parser->setHook('tilegroup', TileGroupWidget::class . '::create');

        $parser->setHook('fontface', FontFaceWidget::class . '::create');
        $parser->setHook('exfont', ExtraFontWidget::class . '::create');
		return true;
    }
}