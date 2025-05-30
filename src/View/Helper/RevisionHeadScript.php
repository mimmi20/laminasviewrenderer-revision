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
use Laminas\View\Exception\BadMethodCallException;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\Minify;
use Mimmi20\LaminasView\Revision\MinifyInterface;

/**
 * RevisionHeadScript
 *
 * dieser View-Helper sollte immer genutzt werden, um JavaScript-Dateien einzubinden
 * erfüllt zwei Aufgaben:
 * - sucht nach Gruppen (Minify.ini), um Gruppendatei statt einzelnen Dateien einzubinden
 * - hängt die Revisionsnummer an den Dateinamen
 *
 * @method string getWhitespace(int|string $indent)
 * @method string getIndent()
 * @method string getSeparator()
 */
final class RevisionHeadScript extends AbstractHelper
{
    use PackageTrait;

    /** @throws void */
    public function __construct(MinifyInterface $minify, private readonly PhpRenderer $renderer)
    {
        $this->minify = $minify;
    }

    /**
     * @param array<string> $attrs
     * @phpstan-param array<string, string> $attrs
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function appendFile(
        string $src,
        string $type = 'text/javascript',
        array $attrs = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $addRevision = true,
    ): self {
        if ($addRevision && $this->minify->isItemOkToAddRevision(Minify::FILETYPE_JS, $src)) {
            $src = $this->minify->addRevision($src);
        }

        $this->renderer->headScript()->appendFile(
            src: $this->getUrl($src, $absolute, $pathPrefix),
            type: $type,
            attrs: $attrs,
        );

        return $this;
    }

    /**
     * @param array<string> $attrs
     * @phpstan-param array<string, string> $attrs
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function prependFile(
        string $src,
        string $type = 'text/javascript',
        array $attrs = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $addRevision = true,
    ): self {
        if ($addRevision && $this->minify->isItemOkToAddRevision(Minify::FILETYPE_JS, $src)) {
            $src = $this->minify->addRevision($src);
        }

        $this->renderer->headScript()->prependFile(
            src: $this->getUrl($src, $absolute, $pathPrefix),
            type: $type,
            attrs: $attrs,
        );

        return $this;
    }
}
