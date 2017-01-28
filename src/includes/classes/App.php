<?php
/**
 * Application.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare(strict_types=1);
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
 * Application.
 *
 * @since 160722.57589 Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 160722.57589 Initial release.
     *
     * @type string Version.
     */
    const VERSION = '170128.2313'; //v//

    /**
     * Constructor.
     *
     * @since 160722.57589 Initial release.
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
                '©acronym' => '<Pre>serve',
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
     * @since 160722.57589 Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        add_filter('the_content', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
        add_filter('the_content', [$this->Utils->Content, 'onTheContentRestore'], 1000);

        add_filter('get_the_excerpt', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
        add_filter('get_the_excerpt', [$this->Utils->Content, 'onTheContentRestore'], 1000);

        add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
        add_filter('if_shortcode_content', [$this->Utils->Content, 'onTheContentRestore'], 1000);

        if (remove_filter('woocommerce_short_description', 'wc_format_product_short_description', 9999999)) {
            // Fix bug: WooCommerce applies markdown after other filters, which can cause corruption.
            add_filter('woocommerce_short_description', 'wc_format_product_short_description', -10000);
        }
        add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentPreserve'], -1000);
        add_filter('woocommerce_short_description', [$this->Utils->Content, 'onTheContentRestore'], 1000);
    }
}
