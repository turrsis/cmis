<?php
namespace Turrsis\Cmis\Utils;

use Zend\Stdlib\ArrayUtils as BaseArrayUtils;

class ArrayUtils extends BaseArrayUtils
{
    public static function iteratorToNestedArray($iterator, $depthField = 'depth', $childsField = 'childs')
    {
        $result = array();
        $parents = array(&$result);
        $currentParent = &$result;

        $depth = is_array($iterator)
                    ? current($iterator)[$depthField]
                    : $iterator->current()[$depthField];

        foreach($iterator as $node) {
            if ($node[$depthField] == $depth + 1) {
                end($currentParent[$childsField]);
                $currentParent = &$currentParent[$childsField][key($currentParent[$childsField])];
                $parents[] = &$currentParent;
            } elseif ($node[$depthField] < $depth) {
                $diff = $node[$depthField] - $depth;
                array_splice($parents, $diff);
                end($parents);
                $currentParent = &$parents[key($parents)];
            }
            $currentParent[$childsField][] = ($node instanceof \Traversable ? iterator_to_array($node) : $node);
            $depth = $node[$depthField];
        }
        return $result[$childsField];
    }
}
