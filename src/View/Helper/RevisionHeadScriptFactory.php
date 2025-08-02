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

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\MinifyInterface;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class RevisionHeadScriptFactory implements FactoryInterface
{
    /**
     * Create Service Factory
     *
     * @param string            $requestedName
     * @param array<mixed>|null $options
     * @phpstan-param array<mixed>|null $options
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    #[Override]
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array | null $options = null,
    ): RevisionHeadScript {
        return new RevisionHeadScript(
            $container->get(MinifyInterface::class),
            $container->get(PhpRenderer::class),
        );
    }
}
