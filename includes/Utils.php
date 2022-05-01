<?php
namespace Isekai\Widgets;

class Utils {
    public static function safeBase64Encode($input) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($input));
    }
}