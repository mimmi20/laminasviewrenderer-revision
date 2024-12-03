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
use SplFileInfo;

use function array_key_exists;
use function assert;
use function file_exists;
use function file_get_contents;
use function in_array;
use function is_array;
use function json_decode;
use function preg_match;
use function sprintf;
use function str_starts_with;

use const JSON_THROW_ON_ERROR;

/**
 * Class Minify for minifying static resources (css/js).
 */
final class Minify implements MinifyInterface
{
    /**
     * Standardrevision
     */
    public const DEFAULT_REVISION = '1';

    /**
     * Postfix
     */
    private const REVISION_STRING = '__%s';

    /**
     * @var array<array<array<string>>>|array<array<string>>
     * @phpstan-var array<string, array{fileName?: string, input?: array<int, string>, attr: array<int, string>|null}>
     */
    private array $groups = [];

    /**
     * Set to detect recursion.
     *
     * @var array<string>
     * @phpstan-var array<int, string>
     */
    private array $packageSet = [];

    /** @throws JsonException */
    public function __construct(
        string | null $groupsFile,
        private readonly string | null $publicDir = null,
        private readonly string | null $revision = null,
        private readonly bool $enabled = false,
    ) {
        if ($groupsFile === null) {
            return;
        }

        $content = @file_get_contents($groupsFile);

        if ($content === false) {
            return;
        }

        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($decoded));

        if (array_key_exists('assets', $decoded) && is_array($decoded['assets'])) {
            $this->groups = $decoded['assets'];
        }
    }

    /**
     * Test for package of given name is known, might by invalid.
     *
     * @throws void
     */
    public function hasPackage(string $package): bool
    {
        return array_key_exists($package, $this->groups);
    }

    /**
     * Returns a list of file(s) if minify is enabled and package exist only the package file, else all files of that
     * package.
     *
     * @return array<array<string>>
     * @phpstan-return array{files?: array<int, string>, attr: array<int, string>}
     *
     * @throws void
     */
    public function getPackageFiles(string $package): array
    {
        if (!$this->isValid($package)) {
            return ['files' => [], 'attr' => []];
        }

        $group = $this->groups[$package];

        if (
            $this->enabled
            && $this->publicDir !== null
            && file_exists($this->publicDir . '/js/min/' . $group['fileName'])
        ) {
            return [
                'files' => ['/js/min/' . $group['fileName']],
                'attr' => (isset($group['attr']) && is_array($group['attr']) ? $group['attr'] : []),
            ];
        }

        $this->packageSet = [$package];

        return [
            'files' => $this->expandSubpackages($group['input']),
            'attr' => (isset($group['attr']) && is_array($group['attr']) ? $group['attr'] : []),
        ];
    }

    /** @throws void */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param MinifyInterface::FILETYPE_* $type (css|js)
     *
     * @throws void
     */
    public function isItemOkToAddRevision(string $type, string $href): bool
    {
        if (!$href) {
            return false;
        }

        if (preg_match('~__[0-9a-f]+~', $href)) {
            return false;
        }

        return $this->isSupportedType($type) && str_starts_with($href, '/');
    }

    /** @throws void */
    public function addRevision(string $resource): string
    {
        $fileInfo = new SplFileInfo($resource);
        $basename = $fileInfo->getBasename('.' . $fileInfo->getExtension());

        return $fileInfo->getPath() . '/' . $basename . $this->getRevisionString() . '.' . $fileInfo->getExtension();
    }

    /**
     * Gibt den Postfix zurück
     *
     * @throws void
     */
    public function getRevisionString(): string
    {
        return sprintf(self::REVISION_STRING, $this->getRevision());
    }

    /**
     * Gibt die aktuelle Revision zurück
     *
     * @throws void
     */
    public function getRevision(): string | null
    {
        return $this->revision;
    }

    /** @throws void */
    private function isValid(string $package): bool
    {
        if (!array_key_exists($package, $this->groups)) {
            return false;
        }

        $group = $this->groups[$package];

        if (
            !array_key_exists('fileName', $group)
            || !array_key_exists('input', $group)
            || $group['fileName'] === ''
        ) {
            return false;
        }

        return $group['input'] !== [];
    }

    /**
     * Adds all files or package files
     *
     * @param array<string> $files
     * @phpstan-param array<int, string> $files
     *
     * @return array<string>
     * @phpstan-return array<int, string>
     *
     * @throws void
     */
    private function expandSubpackages(array $files): array
    {
        $result = [];

        foreach ($files as $partial) {
            if (in_array($partial, $this->packageSet, true)) {
                continue;
            }

            if (!isset($this->groups[$partial])) {
                $result[] = $partial;

                continue;
            }

            $subfiles           = $this->groups[$partial]['input'];
            $this->packageSet[] = $partial;
            $result            += $this->expandSubpackages($subfiles);
        }

        return $result;
    }

    /**
     * Test if item is one of the supportet external resource types.
     *
     * @throws void
     */
    private function isSupportedType(string $type): bool
    {
        return $type === MinifyInterface::FILETYPE_CSS || $type === MinifyInterface::FILETYPE_JS;
    }
}
