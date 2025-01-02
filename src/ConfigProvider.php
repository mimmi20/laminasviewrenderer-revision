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

use Mimmi20\LaminasView\Revision\View\Helper\BaseUrl;
use Mimmi20\LaminasView\Revision\View\Helper\BaseUrlFactory;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionHeadLink;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionHeadLinkFactory;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionHeadScript;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionHeadScriptFactory;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionInlineScript;
use Mimmi20\LaminasView\Revision\View\Helper\RevisionInlineScriptFactory;

final class ConfigProvider
{
    /**
     * Returns configuration from file
     *
     * @return array<array<array<string>>>
     * @phpstan-return array{dependencies: array{factories: array<class-string, class-string>}, view_helpers: array{aliases: array<string|class-string, class-string>, factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getServiceConfig(),
            'view_helpers' => $this->getViewHelperConfig(),
        ];
    }

    /**
     * Get dependency configuration
     *
     * @return array<array<string>>
     * @phpstan-return array{factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                Minify::class => MinifyFactory::class,
            ],
            'aliases' => [
                MinifyInterface::class => Minify::class,
            ],
        ];
    }

    /**
     * Get view helper configuration
     *
     * @return array<array<string>>
     * @phpstan-return array{aliases: array<string|class-string, class-string>, factories: array<class-string, class-string>}
     *
     * @throws void
     */
    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'baseUrl' => BaseUrl::class,
                'revisionHeadLink' => RevisionHeadLink::class,
                'revisionInlineScript' => RevisionInlineScript::class,
                'revisionHeadScript' => RevisionHeadScript::class,
            ],
            'factories' => [
                BaseUrl::class => BaseUrlFactory::class,
                RevisionHeadLink::class => RevisionHeadLinkFactory::class,
                RevisionInlineScript::class => RevisionInlineScriptFactory::class,
                RevisionHeadScript::class => RevisionHeadScriptFactory::class,
            ],
        ];
    }
}
