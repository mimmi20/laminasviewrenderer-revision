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

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\Uri\Uri;

trait GetUrlTrait
{
    /** @throws InvalidArgumentException */
    private function getUrl(string $src, bool $absolute = true, string $pathPrefix = ''): string
    {
        $uri = new Uri($src);

        if ($absolute && $uri->isAbsolute()) {
            $uri->setPath($pathPrefix . $uri->getPath());

            return $uri->toString();
        }

        if ($absolute) {
            return $this->renderer->serverUrl($pathPrefix . $src);
        }

        if ($uri->isAbsolute()) {
            $uri->setScheme(null);
            $uri->setPort(null);
            $uri->setUserInfo(null);
            $uri->setHost(null);
            $uri->setPath($pathPrefix . $uri->getPath());

            return $uri->toString();
        }

        return $pathPrefix . $src;
    }
}
