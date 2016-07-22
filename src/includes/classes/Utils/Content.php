<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Preserve\Pro\Classes\Utils;

use WebSharks\WpSharks\Preserve\Pro\Classes;
use WebSharks\WpSharks\Preserve\Pro\Interfaces;
use WebSharks\WpSharks\Preserve\Pro\Traits;
#
use WebSharks\WpSharks\Preserve\Pro\Classes\AppFacades as a;
use WebSharks\WpSharks\Preserve\Pro\Classes\SCoreFacades as s;
use WebSharks\WpSharks\Preserve\Pro\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Content utils.
 *
 * @since 16xxxx Content utils.
 */
class Content extends SCoreClasses\SCore\Base\Core
{
    /**
     * Tokenizers.
     *
     * @since 16xxxx Content utils.
     *
     * @type CoreClasses\Core\Tokenizer[]|null
     */
    protected $Tokenizers;

    /**
     * Preserve pre/code/samp.
     *
     * @since 16xxxx Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentPreserve($content): string
    {
        $content = (string) $content;

        if (mb_strpos($content, '[') === false) {
            return $content; // Nothing to do.
        } elseif (!preg_match('/\<(?:pre|code|samp)/ui', $content)) {
            return $content; // Nothing to do.
        }
        $Tokenizer          = c::tokenize($content, ['pre', 'code', 'samp']);
        $this->Tokenizers[] = $Tokenizer;
        $content            = $Tokenizer->getString();

        return $content;
    }

    /**
     * Restore pre/code/samp.
     *
     * @since 16xxxx Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentRestore($content): string
    {
        $content = (string) $content;

        if (!$this->Tokenizers || !($Tokenizer = array_pop($this->Tokenizers))) {
            return $content; // Nothing to do.
        } // Pops last tokenizer off the stack ↑ also.

        $Tokenizer->setString($content);
        $content = $Tokenizer->restoreGetString();

        return $content;
    }
}
