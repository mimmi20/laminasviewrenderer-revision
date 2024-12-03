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
use Laminas\View\Renderer\RendererInterface as Renderer;
use Mimmi20\LaminasView\Revision\Minify;
use Mimmi20\LaminasView\Revision\MinifyInterface;

use function array_key_exists;
use function array_merge;
use function array_reverse;
use function is_array;

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
trait PackageTrait
{
    use GetUrlTrait;

    private MinifyInterface $minify;

    /**
     * appends a package
     *
     * @param array<string> $attrs
     * @phpstan-param array<string, string> $attrs
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function appendPackage(
        string $package,
        string $type = 'text/javascript',
        array $attrs = [],
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

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_JS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $this->appendFile(
                $uri,
                $type,
                array_merge($files['attr'] ?? [], $attrs),
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
     * @param array<string> $attrs
     * @phpstan-param array<string, string> $attrs
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function prependPackage(
        string $package,
        string $type = 'text/javascript',
        array $attrs = [],
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

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_JS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $this->prependFile(
                $uri,
                $type,
                array_merge($files['attr'] ?? [], $attrs),
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
     * @param array<string> $attrs
     * @phpstan-param array<string, string> $attrs
     *
     * @return array<array<mixed>>
     * @phpstan-return array<int, array{path: string, type: string, attributes: array<string, string>}>
     *
     * @throws InvalidArgumentException
     */
    public function listPackage(
        string $package,
        string $type = 'text/javascript',
        array $attrs = [],
        bool $absolute = true,
        string $pathPrefix = '',
        bool $clearQuery = false,
    ): array {
        $files = $this->minify->getPackageFiles($package);

        if (!array_key_exists('files', $files) || !is_array($files['files'])) {
            return [];
        }

        $scripts = [];

        foreach ($files['files'] as $file) {
            if (empty($file)) {
                continue;
            }

            $uri = $this->renderer->baseUrl($file, false, $clearQuery);

            if ($uri === '' || $uri === '/') {
                continue;
            }

            if ($this->minify->isItemOkToAddRevision(Minify::FILETYPE_JS, $uri)) {
                $uri = $this->minify->addRevision($uri);
            }

            $scripts[] = [
                'path' => $this->getUrl($uri, $absolute, $pathPrefix),
                'type' => $type,
                'attributes' => array_merge($files['attr'] ?? [], $attrs),
            ];
        }

        return $scripts;
    }

    /**
     * Get the view object
     *
     * @return Renderer|null
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    abstract public function getView();
}
