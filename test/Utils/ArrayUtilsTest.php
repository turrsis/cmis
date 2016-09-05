<?php
namespace TurrsisTest\Cmis\Utils;

use PHPUnit_Framework_TestCase as TestCase;
use Turrsis\Cmis\Utils\ArrayUtils;

class ArrayUtilsTest extends TestCase
{
    public function testIteratorToNestedArray()
    {
        $actual = ArrayUtils::iteratorToNestedArray(array(
            array('name' => 0, 'depth' => 0),
            array('name' => 1, 'depth' => 0),
                array('name' => 2, 'depth' => 1),
                    array('name' => 3, 'depth' => 2),
                    array('name' => 4, 'depth' => 2),
                        array('name' => 5, 'depth' => 3),
                    array('name' => 6, 'depth' => 2),
            array('name' => 7, 'depth' => 0),
        ));
        $expected = array(
            array('name'   => 0,'depth'  => 0,),
            array('name'   => 1,'depth'  => 0,'childs' => array(
                array('name'   => 2,'depth'  => 1,'childs' => array(
                    array('name'   => 3,'depth'  => 2,),
                    array('name'   => 4, 'depth'  => 2, 'childs' => array(
                        array('name'   => 5,'depth'  => 3,),
                    )),
                    array('name'   => 6,'depth'  => 2,),
                )),
            )),
            array('name'   => 7,'depth'  => 0,),
        );
        $this->assertSame($expected, $actual);
        
        $actual = ArrayUtils::iteratorToNestedArray(array(
            array('name' => 0,'depth' => 2),
            array('name' => 1,'depth' => 2),
                array('name' => 2,'depth' => 3),
                    array('name' => 3,'depth' => 4),
                    array('name' => 4,'depth' => 4),
                        array('name' => 5,'depth' => 5),
                    array('name' => 6,'depth' => 4),
            array('name' => 7, 'depth' => 2),
        ));
        $expected = array(
            array('name'   => 0,'depth'  => 2),
            array('name'   => 1,'depth'  => 2,'childs' => array(
                array('name'   => 2,'depth'  => 3,'childs' => array(
                    array('name'   => 3,'depth'  => 4,),
                    array('name'   => 4,'depth'  => 4, 'childs' => array(
                        array('name'   => 5,'depth'  => 5,),
                    )),
                    array('name'   => 6,'depth'  => 4,),
                )),
            )),
            array('name'   => 7,'depth'  => 2),
        );
        $this->assertSame($expected, $actual);
    }

    public function testIteratorToNestedArrayWithOptions()
    {
        $actual = ArrayUtils::iteratorToNestedArray(array(
            array('name' => 0, 'D' => 0),
                new \Zend\Stdlib\ArrayObject(array('name' => 1, 'D' => 1)),
        ), 'D', 'C');
        $expected = array(
            array(
                'name' => 0,
                'D'    => 0,
                'C'    => array(
                    array(
                        'name' => 1,
                        'D'    => 1,
                    ),
                ),
            ),
        );
        $this->assertSame($expected, $actual);
    }
}
