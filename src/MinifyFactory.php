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
use Laminas\ServiceManager\Factory\FactoryInterface;
use Mimmi20\LaminasView\Revision\Config\MinifyConfigInterface;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function assert;

final class MinifyFactory implements FactoryInterface
{
    /**
     * @param string            $requestedName
     * @param array<mixed>|null $options
     * @phpstan-param array<mixed>|null $options
     *
     * @throws JsonException
     * @throws ContainerExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, array | null $options = null): Minify
    {
        $config = $container->get(MinifyConfigInterface::class);
        assert($config instanceof MinifyConfigInterface);

        $enabled  = $config->isEnabled();
        $revision = $config->getRevision();

        if ($revision === null) {
            $revision = Minify::DEFAULT_REVISION;
        }

        return new Minify(
            $config->getGroupsFile(),
            $config->getPublicDir(),
            $revision,
            $enabled,
        );
    }
}
