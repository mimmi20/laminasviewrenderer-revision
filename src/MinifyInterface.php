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

/**
 * Class Minify for minifying static resources (css/js).
 */
interface MinifyInterface
{
    public const FILETYPE_JS = 'js';

    public const FILETYPE_CSS = 'css';

    /**
     * Standardrevision
     *
     * @api
     */
    public const DEFAULT_REVISION = '1';

    /**
     * Test for package of given name is known, might by invalid.
     *
     * @throws void
     */
    public function hasPackage(string $package): bool;

    /**
     * Returns a list of file(s) if minify is enabled and package exist only the package file, else all files of that
     * package.
     *
     * @return array<array<string>>
     * @phpstan-return array{files?: array<int, string>, attr: array<int, string>}
     *
     * @throws void
     */
    public function getPackageFiles(string $package): array;

    /** @throws void */
    public function isEnabled(): bool;

    /**
     * @param self::FILETYPE_* $type (css|js)
     *
     * @throws void
     */
    public function isItemOkToAddRevision(string $type, string $href): bool;

    /** @throws void */
    public function addRevision(string $resource): string;

    /**
     * Gibt den Postfix zurück
     *
     * @throws void
     */
    public function getRevisionString(): string;

    /**
     * Gibt die aktuelle Revision zurück
     *
     * @throws void
     */
    public function getRevision(): string | null;
}
