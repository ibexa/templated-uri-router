<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\TemplatedUriRouter\Tests\Routing\Generator;

use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\RequestContext;
use PHPUnit\Framework\TestCase;

/**
 * @author Baldur Rensch <baldur.rensch@hautelook.com>
 */
class Rfc6570GeneratorTest extends TestCase
{
    /**
     * @dataProvider getTestPlaceholderData
     */
    public function testPlaceholder($expected, $parameters)
    {
        $routes = $this->getRoutes(false);

        $router = new Rfc6570Generator($routes, new RequestContext());

        self::assertEquals($expected, $router->generate('foo', $parameters));
    }

    /**
     * @return array[]
     */
    public function getTestPlaceholderData(): array
    {
        return [
            ['/foo/foobar/{?bar}', ['foo' => 'foobar', 'bar' => 'barbar']],
            ['/foo/foobar/{?bar,paramTwo}', ['foo' => 'foobar', 'bar' => 'barbar', 'paramTwo'=>'paramTwo']],
            ['/foo/foobar/{?bar%3Aencoded}', ['foo' => 'foobar', 'bar:encoded' => 'barbar']],
            ['/foo/foobar/{?bar*}', ['foo' => 'foobar', 'bar' => array()]],
            ['/foo/{placeholder}/{?bar}', ['foo' => '{placeholder}', 'bar' => 'barbar']],
        ];
    }

    public function testPlaceholderInStrictParameter()
    {
        $routes = $this->getRoutes(true);

        $generator = new Rfc6570Generator($routes, new RequestContext());

        self::assertEquals('/foo/{placeholder}/{?bar}', $generator->generate('foo', ['foo' => '{placeholder}', 'bar' => 'barbar']));
    }

    public function testStrictParameters()
    {
        $routes = $this->getRoutes(true);

        $router = new Rfc6570Generator($routes, new RequestContext());

        self::expectException(InvalidParameterException::class);
        $router->generate('foo', ['foo' => 'foobar', 'bar' => 'barbar']);
    }

    public function testLooseParameters()
    {
        $routes = $this->getRoutes(true);

        $router = new Rfc6570Generator($routes, new RequestContext());
        $router->setStrictRequirements(null);

        self::assertEquals('/foo/foobar/{?bar}', $router->generate('foo', ['foo' => 'foobar', 'bar' => 'barbar']));
    }

    protected function getRoutes(bool $isParamRequired = true): array
    {
        $regexp = $isParamRequired ? '\d+' : '.*';

        return [
            'foo' => [
                ['foo'],
                ['foo' => '123'],
                [['text', '/foo/{foo}/']],
                [
                    ['text', '/'],
                    ['variable', '/', $regexp, 'foo', true],
                    ['text', '/foo'],
                ],
                [],
                [],
            ],
        ];
    }
}
