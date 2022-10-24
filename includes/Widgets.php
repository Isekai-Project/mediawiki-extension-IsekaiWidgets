<?php
namespace Isekai\Widgets;

use MapCacheLRU;
use Parser;

class Widgets {
    /**
     * @param \Parser $parser
     */
    public static function onParserSetup(&$parser){
        $parser->extIsekaiWidgetsCache = new MapCacheLRU( 100 ); // 100 is arbitrary

        $parser->setHook('createpage', [CreatePageWidget::class, 'create']);
        $parser->setHook('discoverbox', [DiscoverWidget::class, 'create']);
        $parser->setHook('feedlist', [FeedListWidget::class, 'create']);
        $parser->setHook('previewcard', [PreviewCardWidget::class, 'create']);
        
        $parser->setHook('tile', [TileWidget::class, 'create']);
        $parser->setHook('tilegroup', [TileGroupWidget::class, 'create']);

        $parser->setHook('fontface', [FontFaceWidget::class, 'create']);
        $parser->setHook('exfont', [ExtraFontWidget::class, 'create']);

        $parser->setHook('details', [Html5Widget::class, 'createDetails']);
        $parser->setHook('summary', [Html5Widget::class, 'createSummary']);

        return true;
    }

    public static function onLoad(\OutputPage $outputPage) {
        $outputPage->addModuleStyles("ext.isekai.widgets.global");
        $outputPage->addModuleStyles("ext.isekai.collapse");
    }
}