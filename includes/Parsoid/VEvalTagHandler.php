<?php
namespace Isekai\Widgets\Parsoid;

use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\Ext\ExtensionTagHandler;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;

class VEvalTagHandler extends ExtensionTagHandler {
    public function toArgs(array $extArgs): array {
        $ret = [];
        /** @var KV $extArg */
        foreach ($extArgs as $extArg) {
            $ret[$extArg->k] = $extArg->v;
        }
        return $ret;
    }

    public function sourceToDom(ParsoidExtensionAPI $extApi, string $src, array $extArgs): DocumentFragment {
        $src = preg_replace('/^([ ]*)([#*]+)/', '${1}<nowiki>${2}</nowiki>', $src);
        $args = $this->toArgs($extArgs);

        $type = 'block';
        if (isset($args['inline'])) {
            $type = 'inline';
        }

        $wrapperTag = '';
        $contextType = '';
        switch ($type) {
            case 'inline':
                $wrapperTag = 'span';
                $contextType = 'inline';
                break;
            case 'block':
                $wrapperTag = 'div';
                $contextType = 'block';
                break;
        }
        
        return $extApi->extTagToDOM(
            $extArgs,
            '',
            $src,
            [
                'wrapperTag' => $wrapperTag,
                'parseOpts' => [
                    'extTag' => 'veval',
                    'context' => $contextType
                ],
            ],
        );;
    }
}