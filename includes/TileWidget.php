<?php
namespace Isekai\Widgets;

use Html;
use MediaWiki\MediaWikiServices;
use Title;

class TileWidget {
    private $size = 'medium';
    private $icon = false;
    private $title = '';
    private $href = '';
    private $badge = false;
    private $color = false;
    private $cover = false;
    private $images = [];
    private $grid = false;

    private $attributes = [];

    public function __construct($args, $content){
        $this->content = $content;
        $this->parseArgs($args);
    }

    public static function create(string $text, array $args, \Parser $parser, \PPFrame $frame){
        $parser->getOutput()->addModules(['ext.isekai.tile']);

        $content = '';
        if ($text) {
            $content = $frame->expand($text);

            $title = preg_replace('/\[\[.*?\]\]/', '', $content);
            $title = preg_replace('/<img .*?src="(?<src>.*?)".*?srcset="(?<srcset>.*?)"[^\>]+>/', '', $title);
            $title = strip_tags(trim($title));
            $args['title'] = $title;
        }

        $tile = new TileWidget($args, $content);
        return [$tile->toHtml(), 'markerType' => 'nowiki'];
    }

    private function parseArgs($args){
        $allowedArgs = ['size', 'icon', 'title', 'cover', 'badge', 'color', 'href', 'grid'];

        foreach($args as $name => $arg){
            if(in_array($name, $allowedArgs)){
                $this->$name = $arg;
            } elseif(substr($name, 0, 2) !== 'on'){
                $this->attributes[$name] = $arg;
            }
        }
    }

    private function getSizeArgs(array &$element, array &$content){
        $element['data-size'] = $this->size;
        $element['class'][] = 'tile-' . $this->size;
    }

    private function getColorArgs(array &$element, array &$content){
        if($this->color){
            if(substr($this->color, 0, 1) == '#' || substr($this->color, 0, 3) == 'rgb'){
                $element['style'][] = 'background-color: ' . $this->color;
            } else {
                $color = str_replace($this->color, 'bg-', '');
                $element['class'][] = 'bg-' . $color;
            }
        }
    }

    private function getTitleArgs(array &$element, array &$content){
        if(!empty($this->title)){
            $content[] = Html::element('span', [
                'class' => ['branding-bar'],
            ], $this->title);
            $element['data-title'] = $this->title;
        }
    }

    private function getCoverArgs(array &$element, array &$content){
        $element['data-cover'] = $this->cover;
    }

    private function getHrefArgs(array &$element, array &$content){
        if(substr($this->href, 0, 2) == '[[' && substr($this->href, -2, 2) == ']]'){ //内部链接
            $titleText = substr($this->href, 2, strlen($this->href) - 4);
            $title = Title::newFromText($titleText);
            $href = $title->getLocalURL();
        } else {
            $href = $this->href;
        }
        $element['href'] = $href;
    }

    private function getIconArgs(array &$element, array &$content){
        if($this->icon){
            if(is_string($this->icon)){
                if(preg_match('/\.[a-zA-Z0-9]{3,4}$/', $this->icon)){
                    //图片图标
                    $iconSrc = $this->icon;
                    $type = 'image';
                } else {
                    $iconSrc = explode(' ', $this->icon);
                    $type = 'class';
                }
            } else {
                $type = 'class';
                $iconSrc = $this->icon;
            }

            if($type == 'class'){
                $content[] = Html::element('span', [
                    'class' => array_merge($iconSrc, ['icon']),
                ]);
            } elseif($type == 'image'){
                $content[] = Html::element('img', [
                    'src' => $iconSrc,
                    'class' => ['icon'],
                ]);
            }
        }
    }

    private function getBadgeArgs(array &$element, array &$content){
        if($this->badge){
            $content[] = Html::element('span', [
                'class' => ['badge-bottom'],
            ], strval($this->badge));
        }
    }

    private function getImagesArgs(array &$element, array &$content){
        $service = MediaWikiServices::getInstance();
        $this->images = [];
        // 提取wikitext图片
        preg_match_all('/\[\[(?<title>.+?:.+?)(\|.*?)?\]\]/', $this->content, $matches);
        if (isset($matches['title']) && !empty($matches['title'])) {
            foreach ($matches['title'] as $titleText) {
                $title = Title::newFromText($titleText);
                if ($title->inNamespace(NS_FILE)) {
                    $file = $service->getRepoGroup()->findFile($title);
                    $thumb = $file->getUrl();
                    $this->images[] = $thumb;
                }
            }
        }

        // 提取html图片
        preg_match_all('/<img .*?src="(?<src>.*?)".*?srcset="(?<srcset>.*?)"[^\>]+>/', $this->content, $matches);
        if (isset($matches['src']) && !empty($matches['src'])) {
            $this->images = array_merge($this->images, $matches['src']);
        }

        if(!empty($this->images)){
            $element['data-effect'] = 'image-set';
            foreach($this->images as $image){
                $content[] = Html::element('img', [
                    'src' => $image,
                    'style' => 'display: none'
                ]);
            }
        }
    }

    private function getGridArgs(array &$element, array &$content){
        if($this->grid){
            $grid = explode(' ', $this->grid);
            $element['class'][] = 'col-' . $grid[0];
            if(count($grid) > 1){
                $element['class'][] = 'row-' . $grid[1];
            }
        }
    }

    public function toHtml(){
        $element = array_merge($this->attributes, [
            'data-role' => 'tile',
        ]);
        $content = [];

        if(isset($element['class'])){
            $element['class'] = explode(' ', $element['class']);
        } else {
            $element['class'] = [];
        }
        if(isset($element['style'])){
            $element['style'] = explode(' ', $element['style']);
        } else {
            $element['style'] = [];
        }

        $this->getSizeArgs($element, $content);
        $this->getColorArgs($element, $content);
        $this->getIconArgs($element, $content);
        $this->getTitleArgs($element, $content);
        $this->getCoverArgs($element, $content);
        $this->getHrefArgs($element, $content);
        $this->getBadgeArgs($element, $content);
        $this->getImagesArgs($element, $content);
        $this->getGridArgs($element, $content);

        $content = implode('', $content);

        if(!empty($element['class'])){
            $element['class'] = implode(' ', $element['class']);
        } else {
            unset($element['class']);
        }
        if(!empty($element['style'])){
            $element['style'] = implode('; ', $element['style']) . ';';
        } else {
            unset($element['style']);
        }
        
        return Html::rawElement('a', $element, $content);
    }
}