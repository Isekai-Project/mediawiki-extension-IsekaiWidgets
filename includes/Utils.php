<?php
namespace Isekai\Widgets;

class Utils {
    public static function safeBase64Encode($input) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
    }

    public static function makeParagraph($text, $hasUniq = false) {
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
        return $prepend . "<p>" . implode("</p>\n<p>", $lines) . "</p>" . $append;
    }
}