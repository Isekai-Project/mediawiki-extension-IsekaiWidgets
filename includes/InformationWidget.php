<?php
namespace Isekai\Widgets;

use MediaWiki\MediaWikiServices;

class InformationWidget {
    public static function parseContent($content, $dataMap, $title) {
        $lines = explode("\n", str_replace($content, "\r\n", "\n"));
        $prevData = null;

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
                $prevData = &$data;
                $finalData[] = &$data;
                continue;
            }

            // 动态文本数据
            $sep = Utils::strContains($line, ['=']);
            if ($sep) {
                list($key, $value) = Utils::getKeyValue($sep, $line);
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
                $prevData = &$data;
                $finalData[] = &$data;
                continue;
            }

            // 多行数据，附加到上一行
            if (preg_match('/^[ \t]+/', $line) && $prevData && isset($prevData['text'])) {
                $prevData['text'] .= "\n\n" . trim($line);
                continue;
            }

            if ($lineNum === 0) {
                $title = trim($line);
                continue;
            }

            // 归类为分栏数据
            $data = [
                'type' => 'banner',
                'text' => trim($line)
            ];
            $prevData = &$data;
            $finalData[] = &$data;
        }
        return [$finalData, $title];
    }

    public static function buildText(\Parser $parser, \PPFrame $frame, array $dataMap, $title, $picture, $float) {
        $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig('IsekaiWidget');
        $sep = $config->get('IsekaiWidgetInformationTextSeparator');

        $stringBuilder = [];
        foreach ($dataMap as $information) {
            if ($information['type'] === 'pair') {
                $stringBuilder[] = $information['label'] . $sep .
                    Utils::makeParagraph($information['text'], false, true);
            }
        }
        return [implode('', $stringBuilder), 'markerType' => 'nowiki'];
    }

    public static function buildTable(\Parser $parser, \PPFrame $frame, array $dataMap, $title, $picture, $float) {

    }

    /**
     * @param \Parser $parser
     * @param \PPFrame $frame
     * @param $args
     * @return array|string
     */
    public static function create(\Parser $parser, \PPFrame $frame, $args) {
        $configKeys = ['type', 'float', 'content', 'title_key', 'picture'];
        $configArgs = [];
        $infoArgs = [];

        foreach ($args as $key => $value) {
            if (in_array($key, $configKeys)) {
                $configArgs[$key] = $value;
            } else {
                $infoArgs[$key] = $value;
            }
        }

        $type = $configArgs['type'] ?? 'text';
        $picture = $configArgs['picture'] ?? null;
        $float = $configArgs['float'] ?? '';

        $titleKey = $configArgs['title_key'] ?? null;
        $title = null;

        // 文本模式中，没有title
        if ($type === 'text') {
            $titleKey = null;
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
        if (isset($configArgs['content'])) {
            list($dataMap, $title) = static::parseContent($configArgs['content'], $dataMap, $title);
        }

        switch ($type) {
            case 'text':
                return static::buildText($parser, $frame, $dataMap, $title, $picture, $float);
            default:
                return '<span class="error">' . wfMessage('isekai-information-error-invalid-type')->parse() . '</span>';
        }
    }
}