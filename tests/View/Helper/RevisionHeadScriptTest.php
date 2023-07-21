<?php
/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\View\Helper\Placeholder\Container\AbstractStandalone;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

final class RevisionHeadScriptTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('js', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with(
                'appendFile',
                ['https://www.test.de/abc_42.txt', 'text/javascript', ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class']],
            );

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(4);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headScript): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        1, 4 => self::assertSame('baseUrl', $method),
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headScript', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        4 => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headScript,
                        3 => 'https://www.test.de/abc_42.txt',
                        4 => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage(
            $package,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::never())
            ->method('__call')
            ->with('', []);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::never();
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headScript): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        1, 4 => self::assertSame('baseUrl', $method),
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headScript', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        4 => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headScript,
                        3 => 'https://www.test.de/abc_42.txt',
                        4 => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage(
            $package,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::never())
            ->method('__call')
            ->with('', []);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::never();
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headScript): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        1, 4 => self::assertSame('baseUrl', $method),
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headScript', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        4 => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headScript,
                        3 => 'https://www.test.de/abc_42.txt',
                        4 => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage(
            $package,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::never())
            ->method('__call')
            ->with('', []);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::never();
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headScript): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        1, 4 => self::assertSame('baseUrl', $method),
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headScript', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['abc.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        4 => self::assertSame(['bcd.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headScript,
                        3 => 'https://www.test.de/abc_42.txt',
                        4 => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage(
            $package,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendFile(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('js', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with(
                'appendFile',
                ['https://www.test.de/abc_42.txt', 'text/javascript', ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class']],
            );

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headScript', [])
            ->willReturn($headScript);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendFile(
            $href,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
            true,
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testAppendFile2(): RevisionHeadScript
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with(
                'appendFile',
                ['/abc.txt', 'text/javascript', ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class']],
            );

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headScript', [])
            ->willReturn($headScript);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendFile(
            $href,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
            false,
            '',
            false,
        );

        self::assertSame($object, $return);

        return $return;
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('js', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with('prependFile', ['https://www.test.de/abc_42.txt', 'text/javascript', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(4);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnCallback(
                static function (string $method, array $argv) use ($matcher, $headScript): string | AbstractStandalone {
                    match ($matcher->numberOfInvocations()) {
                        1, 4 => self::assertSame('baseUrl', $method),
                        3 => self::assertSame('serverUrl', $method),
                        2 => self::assertSame('headScript', $method),
                    };

                    match ($matcher->numberOfInvocations()) {
                        1 => self::assertSame(['bcd.txt', false, false], $argv),
                        2 => self::assertSame([], $argv),
                        3 => self::assertSame(['/abc_42.txt'], $argv),
                        4 => self::assertSame(['abc.txt', false, false], $argv),
                    };

                    return match ($matcher->numberOfInvocations()) {
                        1 => '/abc.txt',
                        2 => $headScript,
                        3 => 'https://www.test.de/abc_42.txt',
                        4 => '',
                    };
                },
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependFile(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('js', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with('prependFile', ['https://www.test.de/abc_42.txt', 'text/javascript', []]);

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headScript', [])
            ->willReturn($headScript);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependFile($href);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testPrependFile2(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $headScript = $this->createMock(AbstractStandalone::class);
        $headScript
            ->expects(self::once())
            ->method('__call')
            ->with(
                'prependFile',
                ['/abc.txt', 'text/javascript', ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class']],
            );

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('__call')
            ->with('headScript', [])
            ->willReturn($headScript);
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependFile(
            $href,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
            false,
            '',
            false,
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('js', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $renderer = $this->createMock(PhpRenderer::class);
        $matcher  = self::exactly(3);
        $renderer
            ->expects($matcher)
            ->method('__call')
            ->willReturnMap(
                [
                    ['baseUrl', ['abc.txt', false, false], '/abc.txt'],
                    ['serverUrl', ['/abc_42.txt'], 'https://www.test.de/abc_42.txt'],
                    ['baseUrl', ['bcd.txt', false, false], ''],
                ],
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame(
            [['path' => 'https://www.test.de/abc_42.txt', 'type' => 'text/javascript', 'attributes' => []]],
            $return,
        );
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn([]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => false]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->createMock(MinifyInterface::class);
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => []]);
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }
}
