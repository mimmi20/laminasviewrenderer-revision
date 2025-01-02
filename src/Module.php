<?php

/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Override;

final class Module implements ConfigProviderInterface
{
    /**
     * @return array<array<array<string>>>
     * @phpstan-return array{service_manager: array{factories: array<class-string, class-string>}, view_helpers: array{aliases: array<string|class-string, class-string>, factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    #[Override]
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getServiceConfig(),
            'view_helpers' => $provider->getViewHelperConfig(),
        ];
    }
}
