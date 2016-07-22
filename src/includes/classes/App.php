<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Preserve\Pro\Classes;

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
 * App class.
 *
 * @since 16xxxx Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 16xxxx Initial release.
     *
     * @type string Version.
     */
    const VERSION = '160721.76951'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $instance Instance args.
     */
    public function __construct(array $instance = [])
    {
        $instance_base = [
            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                    ],
                ],
            ],

            '§specs' => [
                '§type' => 'plugin',
                '§file' => dirname(__FILE__, 4).'/plugin.php',
            ],
            '©brand' => [
                '©acronym' => 'PREServe',
                '©name'    => '<Pre>serve',

                '©slug' => 'pre-serve',
                '©var'  => 'pre_serve',

                '©short_slug' => 'pre-serve',
                '©short_var'  => 'pre_serve',

                '©text_domain' => 'pre-serve',
            ],

            '§pro_option_keys' => [],
            '§default_options' => [],
        ];
        parent::__construct($instance_base, $instance);
    }

    /**
     * Other hook setup handler.
     *
     * @since 16xxxx Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        # On `init` w/ a late priority.

        add_action('init', function () {

            # Filters against `the_content`. Covers most WP themes/plugins.

            add_filter('the_content', [$this->Utils->Content, 'onTheContentPreserve'], -100);
            add_filter('the_content', [$this->Utils->Content, 'onTheContentRestore'], 100);

            # Filters against `if_shortcode_content` for compatibility w/ the `[if]` shortcode.

            add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentPreserve'], -100);
            add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentRestore'], 100);

            # Filters against `woocommerce_short_description` for compatibility w/ WooCommerce.

            add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentPreserve'], -100);
            add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentRestore'], 100);

            if (has_filter('woocommerce_short_description', 'wc_format_product_short_description') && s::jetpackCanMarkdown()) {
                // Fixing a WooCommerce bug by Moving Jetpack markdown to a more logical/compatible priority.
                // NOTE: I consider this a bug because WC applies markdown 'after' other filters, which leads to corruption.
                // They also apply `wpautop()` twice. Once via standard filters, then again in `wc_format_product_short_description()`.
                if (remove_filter('woocommerce_short_description', 'wc_format_product_short_description', 9999999)) {
                    add_filter('woocommerce_short_description', s::class.'::jetpackMarkdown', -1000);
                } else { // Flag this as a potential problem whenver debugging is enabled by a developer.
                    debug(0, c::issue([], 'Failed to remove `wc_format_product_short_description()` hook.'));
                }
            }
        }, 100);
    }
}
