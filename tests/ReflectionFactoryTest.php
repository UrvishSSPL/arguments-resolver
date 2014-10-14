<?php

namespace ArgumentsResolver\Tests;

use ArgumentsResolver\Tests\Fixtures\TestClass;
use ArgumentsResolver\ReflectionFactory;

class ReflectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFunctionData
     */
    public function testCreatingReflection($expectedName, $function)
    {
        $reflection = ReflectionFactory::create($function);

        $this->assertInstanceOf('ReflectionFunctionAbstract', $reflection);
        $this->assertSame($expectedName, self::getFunctionName($reflection));
    }

    public function provideFunctionData()
    {
        $testClass = new TestClass();
        $testClassName = get_class($testClass);

        return [
            [$testClassName.'::foo',                        [$testClassName, 'foo']],
            [$testClassName.'::foo',                        [$testClass, 'foo']],
            [$testClassName.'::bar',                        [$testClassName, 'bar']],
            [$testClassName.'::bar',                        $testClassName.'::bar'],
            [$testClassName.'::__construct',                [$testClassName, '__construct']],
            [$testClassName.'::__construct',                [$testClass, '__construct']],
            [$testClassName.'::__invoke',                   $testClass],
            ['Closure::__invoke',                           function () {}],
            [__NAMESPACE__.'\\Fixtures\\function_empty',    __NAMESPACE__.'\\Fixtures\\function_empty'],
        ];
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     *
     * @return string
     */
    protected static function getFunctionName(\ReflectionFunctionAbstract $reflection)
    {
        $name = $reflection->name;

        if ($reflection instanceof \ReflectionMethod) {
            $name = $reflection->getDeclaringClass()->name.'::'.$name;
        }

        return $name;
    }
}