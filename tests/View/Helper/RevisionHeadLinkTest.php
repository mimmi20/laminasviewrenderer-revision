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
use Laminas\View\Exception\BadMethodCallException;
use Laminas\View\Helper\Doctype;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

final class RevisionHeadLinkTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage(): RevisionHeadLink
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::exactly(3))
            ->method('__call')
            ->willReturnMap(
                [
                    ['baseUrl', ['abc.txt', false, false], '/abc.txt'],
                    ['serverUrl', ['/abc_42.txt'], 'https://www.test.de/abc_42.txt'],
                    ['baseUrl', ['bcd.txt', false, false], ''],
                ],
            );
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev']);

        self::assertSame($object, $return);

        return $return;
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage($package, 'screen', '!IE', ['rel' => 'prev', 'async' => null]);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendStylesheet(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendStylesheet($href, 'screen', '!IE', ['rel' => 'prev']);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendStylesheet2(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::any())
            ->method('isXhtml')
            ->willReturn(true);

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::any())
            ->method('plugin')
            ->with('doctype')
            ->willReturn($docType);

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendStylesheet(
            $href,
            'screen',
            '!IE',
            ['rel' => 'prev'],
            false,
            '/test',
            false,
        );

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::exactly(3))
            ->method('__call')
            ->willReturnMap(
                [
                    ['baseUrl', ['bcd.txt', false, false], '/abc.txt'],
                    ['serverUrl', ['/abc_42.txt'], 'https://www.test.de/abc_42.txt'],
                    ['baseUrl', ['abc.txt', false, false], ''],
                ],
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage2(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage3(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependPackage4(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependPackage($package);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependStylesheet(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', $href)
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with($href)
            ->willReturn('https://www.test.de/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependStylesheet($href);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependStylesheet2(): void
    {
        $href = 'https://www.test.de/abc.txt';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::never())
            ->method('getPackageFiles');
        $minify
            ->expects(self::never())
            ->method('isItemOkToAddRevision');
        $minify
            ->expects(self::never())
            ->method('addRevision');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependStylesheet(
            $href,
            'screen',
            '!IE',
            ['rel' => 'prev'],
            false,
            '/test',
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

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::exactly(3))
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

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame(
            [['path' => 'https://www.test.de/abc_42.txt', 'media' => 'screen', 'conditional' => false, 'extra' => []]],
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

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
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

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
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

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::never())
            ->method('__call');
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage5(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::exactly(3))
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

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package, 'print');

        self::assertSame(
            [['path' => 'https://www.test.de/abc_42.txt', 'media' => 'print', 'conditional' => false, 'extra' => []]],
            $return,
        );
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function testListPackage6(): void
    {
        $package = 'test-package';

        $minify = $this->getMockBuilder(MinifyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minify
            ->expects(self::once())
            ->method('getPackageFiles')
            ->with($package)
            ->willReturn(['files' => ['abc.txt', '', 'bcd.txt']]);
        $minify
            ->expects(self::once())
            ->method('isItemOkToAddRevision')
            ->with('css', '/abc.txt')
            ->willReturn(true);
        $minify
            ->expects(self::once())
            ->method('addRevision')
            ->with('/abc.txt')
            ->willReturn('/abc_42.txt');

        $docType = $this->getMockBuilder(Doctype::class)->getMock();
        $docType
            ->expects(self::never())
            ->method('isXhtml');

        $renderer = $this->getMockBuilder(PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderer
            ->expects(self::exactly(2))
            ->method('__call')
            ->willReturnMap(
                [
                    ['baseUrl', ['abc.txt', false, false], '/abc.txt'],
                    ['baseUrl', ['bcd.txt', false, false], ''],
                ],
            );
        $renderer
            ->expects(self::never())
            ->method('plugin');

        $object = new RevisionHeadLink($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package, 'print', false, [], false);

        self::assertSame(
            [['path' => '/abc_42.txt', 'media' => 'print', 'conditional' => false, 'extra' => []]],
            $return,
        );
    }

    /**
     * @throws Exception
     *
     * @depends testAppendPackage
     */
    public function testToStringWithoutIndent(RevisionHeadLink $object): void
    {
        self::assertSame(
            '<!--[if !IE]><!--><link href="https://www.test.de/abc_42.txt" media="screen" rel="stylesheet" type="text/css" /><!--<![endif]-->',
            $object->toString(),
        );
    }

    /**
     * @throws Exception
     *
     * @depends testAppendPackage
     */
    public function testToStringWithIndent(RevisionHeadLink $object): void
    {
        self::assertSame(
            '    <!--[if !IE]><!--><link href="https://www.test.de/abc_42.txt" media="screen" rel="stylesheet" type="text/css" /><!--<![endif]-->',
            $object->toString(4),
        );
    }
}
