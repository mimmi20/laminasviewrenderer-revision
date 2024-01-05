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

namespace Mimmi20\LaminasView\Revision\Config;

interface MinifyConfigInterface
{
    /** @throws void */
    public function isEnabled(): bool;

    /**
     * get the path of the file where the actual git revsion is saved
     *
     * @return string|null Null is returned if the revision is not configured, the revision hash is returned otherwise
     *
     * @throws void
     */
    public function getRevision(): string | null;

    /**
     * get the path of the file where the actual groups/packages are saved
     *
     * @return string|null Null is returned if the file is not configured or the configured file is not readable, the file path is returned otherwise
     *
     * @throws void
     */
    public function getGroupsFile(): string | null;

    /**
     * get the path of the public directory
     *
     * @return string|null Null is returned if the directory is not configured, the path is returned otherwise
     *
     * @throws void
     */
    public function getPublicDir(): string | null;
}
