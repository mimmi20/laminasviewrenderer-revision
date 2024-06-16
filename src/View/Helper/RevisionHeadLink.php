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
use Laminas\View\Exception\BadMethodCallException;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Renderer\PhpRenderer;
use Mimmi20\LaminasView\Revision\Minify;
use Mimmi20\LaminasView\Revision\MinifyInterface;

use function array_key_exists;
use function array_reverse;
use function is_array;

/**
 * RevisionHeadLink
 *
 * @method string getWhitespace(int|string $indent)
 * @method string getIndent()
 * @method string getSeparator()
 */
final class RevisionHeadLink extends AbstractHelper
{
    use GetUrlTrait;

    /** @throws void */
    public function __construct(private readonly MinifyInterface $minify, private readonly PhpRenderer $renderer)
    {
        // nothing to do
    }

    /**
     * appends a package
     *
     * @param string        $package               as given in Minify.ini
     * @param string        $media                 attribute to apply css
     * @param bool|string   $conditionalStylesheet browser compatibility switch
     * @param array<string> $extras                additional attributes like 'async' etc
     * @phpstan-param array<string, string> $extras
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     *
     * @api
     */
    public function appendPackage(
        string $package,
        string $media = 'screen',
        bool | string $conditionalStylesheet = false,
        array $extras = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $clearQuery = false,
    ): self {
        $files = $this->minify->getPackageFiles($package);

        if (!array_key_exists('files', $files) || !is_array($files['files'])) {
            return $this;
        }

        foreach ($files['files'] as $file) {
            if (empty($file)) {
                continue;
            }

            $uri = $this->renderer->baseUrl($file, false, $clearQuery);

            if ($uri === '' || $uri === '/') {
                continue;
            }

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $this->appendStylesheet(
                $uri,
                $media,
                $conditionalStylesheet,
                $extras,
                $absolute,
                $pathPrefix,
                false,
            );
        }

        return $this;
    }

    /**
     * prepends a package
     *
     * @param string        $package               as given in Minify.ini
     * @param string        $media                 attribute to apply css
     * @param bool|string   $conditionalStylesheet browser compatibility switch
     * @param array<string> $extras                additional attributes like 'async' etc
     * @phpstan-param array<string, string> $extras
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     *
     * @api
     */
    public function prependPackage(
        string $package,
        string $media = 'screen',
        bool | string $conditionalStylesheet = false,
        array $extras = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $clearQuery = false,
    ): self {
        $files = $this->minify->getPackageFiles($package);

        if (!array_key_exists('files', $files) || !is_array($files['files'])) {
            return $this;
        }

        foreach (array_reverse($files['files']) as $file) {
            if (empty($file)) {
                continue;
            }

            $uri = $this->renderer->baseUrl($file, false, $clearQuery);

            if ($uri === '' || $uri === '/') {
                continue;
            }

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $this->prependStylesheet(
                $uri,
                $media,
                $conditionalStylesheet,
                $extras,
                $absolute,
                $pathPrefix,
                false,
            );
        }

        return $this;
    }

    /**
     * appends a package
     *
     * @param string        $package               as given in Minify.ini
     * @param string        $media                 attribute to apply css
     * @param bool|string   $conditionalStylesheet browser compatibility switch
     * @param array<string> $extras                additional attributes like 'async' etc
     * @phpstan-param array<string, string> $extras
     *
     * @return array<array<mixed>>
     * @phpstan-return array<int, array{path: string, media: string, conditional: bool|string, extra: array<string, string>}>
     *
     * @throws InvalidArgumentException
     *
     * @api
     */
    public function listPackage(
        string $package,
        string $media = 'screen',
        bool | string $conditionalStylesheet = false,
        array $extras = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $clearQuery = false,
    ): array {
        $files = $this->minify->getPackageFiles($package);

        if (!array_key_exists('files', $files) || !is_array($files['files'])) {
            return [];
        }

        $styles = [];

        foreach ($files['files'] as $file) {
            if (empty($file)) {
                continue;
            }

            $uri = $this->renderer->baseUrl($file, false, $clearQuery);

            if ($uri === '' || $uri === '/') {
                continue;
            }

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $styles[] = [
                'path' => $this->getUrl($uri, $absolute, $pathPrefix),
                'media' => $media,
                'conditional' => $conditionalStylesheet,
                'extra' => $extras,
            ];
        }

        return $styles;
    }

    /**
     * @param string        $media                 attribute to apply css
     * @param bool|string   $conditionalStylesheet browser compatibility switch
     * @param array<string> $extras                additional attributes like 'async' etc
     * @phpstan-param array<string, string> $extras
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function appendStylesheet(
        string $href,
        string $media = 'screen',
        bool | string $conditionalStylesheet = false,
        array $extras = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $addRevision = true,
    ): self {
        if ($addRevision && $this->minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, $href)) {
            $href = $this->minify->addRevision($href);
        }

        unset($extras['rel']);

        $this->renderer->headLink()->appendStylesheet(
            $this->getUrl($href, $absolute, $pathPrefix),
            $media,
            $conditionalStylesheet,
            $extras,
        );

        return $this;
    }

    /**
     * @param string        $media                 attribute to apply css
     * @param bool|string   $conditionalStylesheet browser compatibility switch
     * @param array<string> $extras                additional attributes like 'async' etc
     * @phpstan-param array<string, string> $extras
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function prependStylesheet(
        string $href,
        string $media = 'screen',
        bool | string $conditionalStylesheet = false,
        array $extras = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $addRevision = true,
    ): self {
        if ($addRevision && $this->minify->isItemOkToAddRevision(Minify::FILETYPE_CSS, $href)) {
            $href = $this->minify->addRevision($href);
        }

        unset($extras['rel']);

        $this->renderer->headLink()->prependStylesheet(
            $this->getUrl($href, $absolute, $pathPrefix),
            $media,
            $conditionalStylesheet,
            $extras,
        );

        return $this;
    }
}
