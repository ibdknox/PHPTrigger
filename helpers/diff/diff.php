<?php

include("Text_Diff.php");
include("Diff/Renderer.php");
include("Diff/Renderer/inline.php");

class diff {

    static function inline($first, $second) {

        if( !is_string ( end($first) ) ) {
            $first = self::transform($first[0]);
            $second = self::transform($second[0]);
        }

        $diff = new Text_Diff('auto', array($first, $second));
        $renderer = new Text_Diff_Renderer_inline();
        return $renderer->render($diff);
    }

    static function transform($arg) {
        return explode("\r\n", print_r($arg, true));
    }

}
