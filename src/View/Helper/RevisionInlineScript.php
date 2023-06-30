<?php
/**
 * This file is part of the mimmi20/laminasviewrenderer-revision package.
 *
 * Copyright (c) 2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\LaminasView\Revision\View\Helper;

use Laminas\View\Helper\InlineScript;
use Mimmi20\LaminasView\Revision\MinifyInterface;

/**
 * RevisionInlineScript
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
final class RevisionInlineScript extends InlineScript
{
    use PackageTrait;

    /**
     * Flag whether to automatically escape output, must also be
     * enforced in the child class if __toString/toString is overridden
     *
     * @var bool
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $autoEscape = false;

    /**
     * Optional allowed attributes for script tag
     *
     * @var array<string>
     * @phpstan-var array<int, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $optionalAttributes = [
        'charset',
        'integrity',
        'crossorigin',
        'defer',
        'async',
        'language',
        'src',
        'id',
        'class',
    ];

    /** @throws void */
    public function __construct(MinifyInterface $minify)
    {
        parent::__construct();

        $this->minify = $minify;
    }
}
