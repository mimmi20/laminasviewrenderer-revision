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

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\Uri\Exception\InvalidArgumentException;
use Laminas\Uri\Http;
use Laminas\View\Helper\AbstractHelper;

/**
 * Erweitert den Laminas Base Url Helper um einen optionalen Parameter, über den die komplette URL zurückgegeben wird.
 */
final class BaseUrl extends AbstractHelper
{
    /** @throws void */
    public function __construct(private readonly Http | null $uri = null)
    {
        // nothing to do
    }

    /**
     * Erweitert die normale Laminas Funktionalität, um die Möglichkeit sich die komplette URL
     * inkl. Protocol und Host zurückgegeben zu lassen
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(string $resource, bool $prependProtocolAndHost = false, bool $clearQuery = false): string
    {
        $uri = $this->uri === null ? new Http() : clone $this->uri;

        $uri->setPath($resource);

        if (!$prependProtocolAndHost || $this->uri === null) {
            // Remove host, port and scheme
            $uri->setHost(null)
                ->setPort(null)
                ->setScheme(null);
        } else {
            $scheme = $this->uri->getScheme();
            $host   = $this->uri->getHost();
            $port   = $this->uri->getPort();

            $uri->setHost($host)
                ->setPort($port)
                ->setScheme($scheme);
        }

        if ($clearQuery) {
            $uri->setQuery(null);
        }

        return $uri->toString();
    }
}
