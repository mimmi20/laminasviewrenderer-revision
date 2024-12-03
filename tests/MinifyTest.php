<?php

/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision;

use JsonException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

final class MinifyTest extends TestCase
{
    /** @throws JsonException */
    public function testConstructFailsInvalidContent(): void
    {
        $configFile = 'configTest.php';
        $structure  = [$configFile => 'some text content'];

        vfsStream::setup('root', null, $structure);

        $this->expectException(JsonException::class);
        $this->expectExceptionCode(4);
        $this->expectExceptionMessage('Syntax error');

        new Minify(vfsStream::url('root/' . $configFile), null, null, false);
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function testConstructFailsIfFileIsNotReadable(): void
    {
        $configFile = 'configTest.php';
        $structure  = [$configFile => null];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(vfsStream::url('root/' . $configFile), null, null, false);

        $groupsProp = new ReflectionProperty($minify, 'groups');

        $groups = $groupsProp->getValue($minify);

        self::assertIsArray($groups);
        self::assertCount(0, $groups);
        self::assertFalse($minify->hasPackage('detect-js'));
        self::assertFalse($minify->isEnabled());
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function testConstructOk(): Minify
    {
        $configFile = 'configTest.php';
        $structure  = [
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "fileName": "detection.min.js",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(vfsStream::url('root/' . $configFile), null, null, true);

        $groupsProp = new ReflectionProperty($minify, 'groups');

        $groups = $groupsProp->getValue($minify);

        self::assertIsArray($groups);
        self::assertCount(1, $groups);
        self::assertArrayHasKey('detect-js', $groups);
        self::assertTrue($minify->hasPackage('detect-js'));
        self::assertTrue($minify->isEnabled());

        return $minify;
    }

    /** @throws Exception */
    #[Depends('testConstructOk')]
    public function testGetPackageFilesWithoutMerging(Minify $minify): void
    {
        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(4, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesWithoutMergingWhenDisabled(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group      = 'detection.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [$group => 'console.log();'],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "fileName": "' . $group . '",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            false,
        );

        self::assertFalse($minify->isEnabled());

        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(4, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesWithMerging(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group      = 'detection.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [$group => 'console.log();'],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "fileName": "' . $group . '",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            true,
        );

        self::assertTrue($minify->isEnabled());

        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(1, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesWhenInvalidMissingGroup(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group      = 'detection.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [$group => 'console.log();'],
                ],
            ],
            $configFile => '{
    "assets": {
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            true,
        );

        self::assertTrue($minify->isEnabled());

        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(0, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesWhenInvalidMissingFilename(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group      = 'detection.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [$group => 'console.log();'],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            true,
        );

        self::assertTrue($minify->isEnabled());

        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(0, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesWhenInvalidMissingInput(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group      = 'detection.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [$group => 'console.log();'],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "fileName": "' . $group . '",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            true,
        );

        self::assertTrue($minify->isEnabled());

        $files = $minify->getPackageFiles('detect-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(0, $files['files']);
    }

    /** @throws JsonException */
    public function testIsItemOkToAddRevisionFailMissingHref(): void
    {
        $minify = new Minify(null, null, null, true);

        self::assertFalse($minify->isItemOkToAddRevision('', ''));
    }

    /** @throws JsonException */
    public function testIsItemOkToAddRevisionFailAlreadyMinifiedHref(): void
    {
        $minify = new Minify(null, null, null, true);

        self::assertFalse($minify->isItemOkToAddRevision('', 'ab__123.txt'));
    }

    /** @throws JsonException */
    public function testIsItemOkToAddRevisionFailWrongType(): void
    {
        $minify = new Minify(null, null, null, true);

        self::assertFalse($minify->isItemOkToAddRevision('txt', 'ab.txt'));
    }

    /** @throws JsonException */
    public function testIsItemOkToAddRevisionOk(): void
    {
        $minify = new Minify(null, null, null, true);

        self::assertTrue($minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, '/ab.css'));
    }

    /** @throws JsonException */
    public function testAddRevisionWithoutRevisionFile(): void
    {
        $minify = new Minify(null, null, Minify::DEFAULT_REVISION, true);

        $newName = $minify->addRevision('abc.txt');

        self::assertSame('/abc__1.txt', $newName);
    }

    /** @throws JsonException */
    public function testAddRevisionWithRevisionFileAndDefaultRevision(): void
    {
        $revisionFile = 'configTest.php';
        $revision     = '565656556';
        $structure    = [$revisionFile => $revision];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(null, null, $revision, true);

        $newName = $minify->addRevision('abc.txt');

        self::assertSame('/abc__' . $revision . '.txt', $newName);
    }

    /** @throws JsonException */
    public function testAddRevisionWithRevisionFileAndDefaultRevision2(): void
    {
        $revisionFile = 'configTest.php';
        $revision     = '565656556';
        $structure    = [$revisionFile => $revision];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(null, null, $revision, true);

        $newName = $minify->addRevision('abc.txt');

        self::assertSame('/abc__' . $revision . '.txt', $newName);
    }

    /** @throws JsonException */
    public function testGetPackageFilesFailsRecursion(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group1     = 'detection.min.js';
        $group2     = 'admin.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [
                        $group1 => 'console.log();',
                        $group2 => 'console.log();',
                    ],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "admin-js",
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "fileName": "' . $group1 . '",
            "attr": null,
            "include": true
        },
        "admin-js": {
            "input": [
                "detect-js",
                "/assets/js/lib/detection/base.js",
                "/js/lib/bootstrap-datepicker.min.js",
                "/js/lib/bootstrap-datepicker.de.min.js"
            ],
            "fileName": "' . $group2 . '",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            false,
        );

        self::assertFalse($minify->isEnabled());

        $files = $minify->getPackageFiles('admin-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(7, $files['files']);
    }

    /** @throws JsonException */
    public function testGetPackageFilesFailsRecursion2(): void
    {
        $publicDir  = 'public';
        $configFile = 'configTest.php';
        $group1     = 'detection.min.js';
        $group2     = 'admin.min.js';
        $structure  = [
            $publicDir => [
                'js' => [
                    'min' => [
                        $group1 => 'console.log();',
                        $group2 => 'console.log();',
                    ],
                ],
            ],
            $configFile => '{
    "assets": {
        "detect-js": {
            "input": [
                "admin-js",
                "/assets/js/lib/detection/modernizr-custom.js",
                "/assets/js/lib/detection/detectizr.js",
                "/assets/js/lib/detection/ua-parser.js",
                "/assets/js/lib/detection/base.js"
            ],
            "fileName": "' . $group1 . '",
            "attr": null,
            "include": true
        },
        "admin-js": {
            "input": [
                "detect-js",
                "/assets/js/lib/detection/base.js",
                "/js/lib/bootstrap-datepicker.min.js",
                "/js/lib/bootstrap-datepicker.de.min.js"
            ],
            "fileName": "' . $group2 . '",
            "attr": null,
            "include": true
        }
    }
}',
        ];

        vfsStream::setup('root', null, $structure);

        $minify = new Minify(
            vfsStream::url('root/' . $configFile),
            vfsStream::url('root/' . $publicDir),
            null,
            false,
        );

        self::assertFalse($minify->isEnabled());

        $files = $minify->getPackageFiles('admin-js');

        self::assertCount(2, $files);
        self::assertArrayHasKey('files', $files);
        self::assertCount(7, $files['files']);
    }
}
