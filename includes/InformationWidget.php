<?php
namespace Isekai\Widgets;

use Html;
use MediaWiki\MediaWikiServices;

class InformationWidget {
    public static function parseContent($content, $dataMap, $title) {
        $lines = explode("\n", str_replace("\r\n", "\n", $content));
        $prevDataKey = null;

        $finalData = [];
        foreach ($lines as $lineNum => $line) {
            // 静态文本数据
            $sep = Utils::strContains($line, [':', '：']);
            if ($sep) {
                list($key, $value) = Utils::getKeyValue($sep, $line);
                $data = [
                    'type' => 'pair',
                    'label' => $key,
                    'text' => $value,
                ];
                $finalData[] = $data;
                $prevDataKey = count($finalData) - 1;
                continue;
            }

            // 动态文本数据
            $sep = Utils::strContains($line, ['=']);
            if ($sep) {
                list($key, $value) = Utils::getKeyValue($sep, $line);
                if ($key === '') { // While text is '= key'
                    $key = $value;
                }
                if (isset($dataMap[$value])) {
                    $data = [
                        'type' => 'pair',
                        'label' => $key,
                        'text' => $dataMap[$value],
                    ];
                } else {
                    $data = [
                        'type' => 'pair',
                        'label' => $key,
                        'text' => '#' . $value,
                    ];
                }
                $finalData[] = $data;
                $prevDataKey = count($finalData) - 1;
                continue;
            }

            // 多行数据，附加到上一行
            /* 暂时仅支持<br>
            if (preg_match('/^[ \t]+/', $line) && $prevDataKey !== null) {
                $finalData[$prevDataKey]['text'] .= "\n\n" . trim($line);
                continue;
            }
            */

            if ($lineNum === 0) {
                $title = trim($line);
                continue;
            }

            // 归类为分栏数据
            $data = [
                'type' => 'banner',
                'text' => trim($line)
            ];
            $finalData[] = $data;
            $prevDataKey = count($finalData) - 1;
        }
        return [$finalData, $title];
    }
    
    public static function parseMap($dataMap) {
        $finalData = [];
        foreach ($dataMap as $key => $value) {
            $finalData[] = [
                'type' => 'pair',
                'label' => $key,
                'text' => $value
            ];
        }
        return $finalData;
    }

    public static function buildText(\Parser $parser, \PPFrame $frame, array $dataMap, $title, $picture, $float) {
        global $wgIsekaiWidgetInformationTextSeparator;
        $sep = $wgIsekaiWidgetInformationTextSeparator;

        $lines = [];
        foreach ($dataMap as $information) {
            if ($information['type'] === 'pair') {
                $lines[] = $information['label'] . $sep . $information['text'];
            }
        }
        $html = implode("\n\n", $lines);
        $html = str_replace("\n", "\r\n", $html);
        $html = $parser->recursiveTagParseFully($html, $frame);
        return [$html, 'markerType' => 'nowiki'];
    }

    public static function buildInfoBox(\Parser $parser, \PPFrame $frame, array $dataMap, $title, $picture, $float) {
        $parser->getOutput()->addModules(['ext.isekai.information.infobox']);

        $tableClasses = ['wikitable-container', 'infobox'];
        if ($float === 'right') {
            $tableClasses[] = 'infobox-float-right';
        } else if ($float === 'left') {
            $tableClasses[] = 'infobox-float-left';
        }
        $htmlBuilder = [
            Html::openElement('div', [
                'class' => implode(' ', $tableClasses)
            ]) . Html::openElement('table', [
                'class' => 'wikitable'
            ])
        ];
        if (is_string($title) && $title !== '') {
            $htmlBuilder[] = Html::rawElement('thead', [], 
                Html::rawElement('th', ['colspan' => 2, 'class' => 'infobox-title'], 
                    $parser->recursiveTagParse($title, $frame)
                )
            );
        }
        $htmlBuilder[] = Html::openElement('tbody');
        if (is_string($picture) && $picture !== '') {
            $htmlBuilder[] = Html::rawElement('tr', [], 
                Html::rawElement('td', ['colspan' => 2, 'class' => 'infobox-picture'], $parser->recursiveTagParse("[[$picture|frameless]]", $frame))
            );
        }
        foreach ($dataMap as $information) {
            switch ($information['type']) {
                case 'pair':
                    $htmlBuilder[] = Html::rawElement('tr', [], 
                        Html::rawElement('td', [], $parser->recursiveTagParse($information['label'], $frame)) .
                        Html::rawElement('td', ['style' => 'text-align:center'], $parser->recursiveTagParse($information['text'], $frame))
                    );
                    break;
                case 'banner':
                    $htmlBuilder[] = Html::rawElement('tr', [], 
                        Html::rawElement('td', ['colspan' => 2, 'class' => 'infobox-banner'], $parser->recursiveTagParse($information['text'], $frame))
                    );
                    break;
                    
            }
        }
        $htmlBuilder[] = Html::closeElement('tbody') . Html::closeElement('table') . Html::closeElement('div');
        $html = implode('', $htmlBuilder);
        return [$html, 'markerType' => 'nowiki'];
    }

    /**
     * @param string $content
     * @param array $args
     * @param \Parser $parser
     * @param \PPFrame $frame
     * @return array|string
     */
    public static function create(string $content, array $args, \Parser $parser, \PPFrame $frame) {
        $configKeys = ['type', 'float', 'title_key', 'picture'];
        $configArgs = [];
        $infoArgs = [];

        foreach ($args as $key => $value) {
            if (in_array($key, $configKeys)) {
                $configArgs[$key] = $value;
            } else {
                $infoArgs[$key] = $value;
            }
        }

        $type = strtolower($configArgs['type']) ?? 'text';
        $picture = $configArgs['picture'] ?? null;
        $float = $configArgs['float'] ?? '';

        $titleKey = $configArgs['title_key'] ?? null;
        $title = null;

        if ($type === 'text') {
            // 文本模式中，没有title
            $titleKey = null;
        } else if ($type === 'infobox') {
            // 信息框默认居右
            if ($float === '') {
                $float = 'right';
            }
        }

        $dataMap = [];
        foreach ($infoArgs as $key => $value) {
            if ((is_int($key) && !$titleKey) || ($titleKey && $key === $titleKey)) {
                $title = $value;
                unset($dataMap[$key]);
            } else {
                $dataMap[$key] = $value;
            }
        }
        if (trim($content) !== '') {
            list($dataMap, $title) = static::parseContent($content, $dataMap, $title);
        } else {
            $dataMap = static::parseMap($dataMap);
            if ($type === 'infobox') {
                array_unshift($dataMap, [
                    'type' => 'banner',
                    'text' => wfMessage('isekai-information-title-base-information')->parse()
                ]);
            }
        }

        switch ($type) {
            case 'text':
                return static::buildText($parser, $frame, $dataMap, $title, $picture, $float);
            case 'infobox':
                return static::buildInfoBox($parser, $frame, $dataMap, $title, $picture, $float);
            default:
                return '<span class="error">' . wfMessage('isekai-information-error-invalid-type')->parse() . '</span>';
        }
    }
}