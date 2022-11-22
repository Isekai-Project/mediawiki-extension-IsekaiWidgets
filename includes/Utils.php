<?php
namespace Isekai\Widgets;

class Utils {
    public static function safeBase64Encode($input) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
    }

    public static function makeParagraph($text, $hasUniq = false, $indent = false) {
        $text = str_replace("\r\n", "\n", $text);
        if (strpos($text, "\n\n") === false) {
            return $text;
        }
        $prepend = "";
        $append = "";
        if ($hasUniq) {
            $splitPoint = strpos($text, "\n", 1) + 1;
            $prepend = substr($text, 0, $splitPoint);
            $text = substr($text, $splitPoint);
        }
        preg_match("/(\<\/div[^\>]*?\>|\n)+$/", $text, $matches);
        if (count($matches) > 0) {
            $append = $matches[0];
            $text = substr($text, 0, -1 * strlen($append));
            $tagNum = substr_count($append, '</div');
            preg_match('/^(\<div[^\>]*?\>){' . $tagNum . '}/', $text, $matches);
            if (count($matches) > 0) {
                $prepend .= $matches[0];
                $text = substr($text, strlen($matches[0]));
            }
        }

        $lines = explode("\n\n", $text);

        $stringBuilder = [$prepend];
        foreach ($lines as $lineNum => $line) {
            if ($lineNum > 0 && $indent) {
                $elemAttr = [
                    'class' => 'paragraph-indent'
                ];
            } else {
                $elemAttr = [];
            }
            $stringBuilder[] = \Html::rawElement('p', $elemAttr, $line);
        }
        $stringBuilder[] = $append;
        return implode('', $stringBuilder);
    }

    public static function strContains(string $haystack, array $needle) {
        foreach ($needle as $one) {
            if (strpos($haystack, $one) !== false) {
                return $one;
            }
        }
        return false;
    }

    public static function trimEach(array $arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = trim($value);
        }
        return $arr;
    }

    public static function getKeyValue(string $separator, string $str) {
        $sepLen = strlen($separator);
        $sepOffset = strpos($str, $separator);
        if ($sepOffset === false) {
            return [null, $str];
        } else {
            $key = trim(substr($str, 0, $sepOffset));
            $value = trim(substr($str, $sepOffset + $sepLen));
            return [$key, $value];
        }
    }
}