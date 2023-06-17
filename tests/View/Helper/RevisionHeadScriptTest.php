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
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

final class RevisionHeadScriptTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendPackage(): RevisionHeadScript
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
            ->with('js', '/abc.txt')
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

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->appendPackage(
            $package,
            'text/javascript',
            ['rel' => 'prev', 'async' => null, 'conditional' => '!IE', 'class' => 'test-class'],
        );

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
     * @throws BadMethodCallException
     */
    public function testAppendFile(): RevisionHeadScript
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
            ->with('js', $href)
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

        return $return;
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testAppendFile2(): RevisionHeadScript
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
            ->with('js', '/abc.txt')
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

        $object = new RevisionHeadScript($minify);
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

        $object = new RevisionHeadScript($minify);
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

        $object = new RevisionHeadScript($minify);
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

        $object = new RevisionHeadScript($minify);
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
    public function testPrependFile(): void
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
            ->with('js', $href)
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

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->prependFile($href);

        self::assertSame($object, $return);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function testPrependFile2(): void
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
            ->with('js', '/abc.txt')
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

        $object = new RevisionHeadScript($minify);
        $view   = new ReflectionProperty($object, 'view');
        $view->setValue($object, $renderer);

        $return = $object->listPackage($package);

        self::assertSame([], $return);
    }

    /** @throws Exception */
    #[Depends('testAppendPackage')]
    public function testToStringWithoutIndent(RevisionHeadScript $object): void
    {
        self::assertSame(
            '<!--[if !IE]><!--><script type="text/javascript" async="async" class="test-class" src="https://www.test.de/abc_42.txt"></script><!--<![endif]-->',
            $object->toString(),
        );
    }

    /** @throws Exception */
    #[Depends('testAppendPackage')]
    public function testToStringWithIndent(RevisionHeadScript $object): void
    {
        self::assertSame(
            '    <!--[if !IE]><!--><script type="text/javascript" async="async" class="test-class" src="https://www.test.de/abc_42.txt"></script><!--<![endif]-->',
            $object->toString(4),
        );
    }
}
