<?php
/**
 * Content utils.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare(strict_types=1);
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
 * @since 160722.57589 Content utils.
 */
class Content extends SCoreClasses\SCore\Base\Core
{
    /**
     * Tokenizers.
     *
     * @since 160722.57589 Content utils.
     *
     * @type array|null
     */
    protected $Tokenizers;

    /**
     * Preserve pre/code/samp.
     *
     * @since 160722.57589 Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentPreserve($content): string
    {
        $content = (string) $content;

        if (!$content) {
            $this->Tokenizers[] = null;
            return $content; // Nothing to do.
        } elseif (!preg_match('/\<(?:pre|code|samp)/ui', $content)) {
            $this->Tokenizers[] = null;
            return $content; // Nothing to do.
        }
        // Note: The documentation for this plugin states that it also preserves
        // content in backticks. That's not exactly true, but it is Markdown compatible.

        // Assuming that Jetpack (or similar) is being used for Markdown parsing.
        // So backticks will have already been transformed into `<pre>` or `<code>` tags.
        // All we are concerned with in the content filtering phase is pure HTML markup.

        $Tokenizer          = c::tokenize($content, ['pre', 'code', 'samp']);
        $this->Tokenizers[] = $Tokenizer; // End of stack.
        return $content     = $Tokenizer->getString();
    }

    /**
     * Restore pre/code/samp.
     *
     * @since 160722.57589 Content utils.
     *
     * @param string|scalar $content Content.
     *
     * @return string $content Filtered content.
     */
    public function onTheContentRestore($content): string
    {
        $content = (string) $content;

        if (!$this->Tokenizers) {
            debug(0, c::issue(vars(), 'Missing tokenizers.'));
            return $content; // Nothing to do.
        } elseif (!($Tokenizer = array_pop($this->Tokenizers))) {
            return $content; // Nothing to do.
        } // Also pops last tokenizer off the stack.

        $Tokenizer->setString($content); // As it exists now.

        return $content = $Tokenizer->restoreGetString();
    }
}
