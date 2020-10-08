<?php
/**
 * (a)squared ads
 *
 * @package     (a)squared ads
 * @author      (a)squaredstudio
 * @copyright   2020 (a)squaredstudio
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: (a)squared ads
 * Plugin URI:  https://asquaredstudio.com
 * Description: A simple managed ad plugin that uses ACF 5.0 as it core
 * Version:     0.0.1
 * Author:      (a)squaredstudio
 * Author URI:  https://asquaredstudio.com
 * Text Domain: simpleacfads
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/asquaredstudio/asquared-ads
 */

require_once('classes/Ads.Class.php');
new Ads();
