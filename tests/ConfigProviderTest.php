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

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    /** @throws Exception */
    public function testGetViewHelperConfig(): void
    {
        $object = new ConfigProvider();
        $config = $object->getViewHelperConfig();

        self::assertIsArray($config);
        self::assertArrayHasKey('aliases', $config);
        self::assertArrayHasKey('factories', $config);
    }

    /** @throws Exception */
    public function testGetServiceConfig(): void
    {
        $object = new ConfigProvider();
        $config = $object->getServiceConfig();

        self::assertIsArray($config);
        self::assertArrayHasKey('factories', $config);
    }

    /** @throws Exception */
    public function testInvoke(): void
    {
        $object = new ConfigProvider();
        $config = $object();

        self::assertIsArray($config);
        self::assertArrayHasKey('dependencies', $config);
        self::assertArrayHasKey('view_helpers', $config);
    }
}
