<?php
/**
 * Plugin Name
 *
 * @package     Lki Feed
 * @author      Jan Arambulo
 * @copyright   2018 Jan Arambulo
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Lki Feed
 * Plugin URI:  https://github.com/najaram/lkifeed
 * Description: A basic linkedin plugin that displays user post feed
 * Version:     1.0.0
 * Author:      Jan Arambulo
 * Author URI:  https://najaram.github.io/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

require_once dirname(__FILE__).'/lib/lkifeedapi.php';

use LkiFeedApi\LkiFeedApi;

class LkiFeed
{
	protected $lkifeed;

	public function __construct()
	{
		try {
			$clientId = get_option('lki_client_id');
			$clientSecret = get_option('lki_client_secret');
			$clientRedirect = get_option('lki_client_redirect');

			$this->lkifeed = new LkiFeedApi($clientId, $clientSecret, $clientRedirect);


		} catch (\Exception $e) {
			$e->getMessage();
		}
	}

	public function getfeedApi()
	{
		if (!$this->lkifeed) {
			throw new \Exception('Something went wrong.');
		}

		return $this->lkifeed;
	}
}

/*--------------------------------------------------------------------------------------------
| Options
|--------------------------------------------------------------------------------------------*/
function lkiAdminOptionsPage()
{
	add_options_page('Lki Feed', 'Lki Feed', 'manage_options', 'lkifeed', 'lkiOptionsPage');
}

function lkiAddOptions() {
    add_option('lki_client_id', '');
    add_option('lki_client_secret', '');
    add_option('lki_client_redirect', '');
}

if (is_admin()) {
	add_action('admin_init', 'lkiAddOptions');
	add_action('admin_menu', 'lkiAdminOptionsPage');
}

function lkiOptionsPage() {

	if ($_POST) {
		lkiUpdateOptions($_POST);
	}

	$lkifeed = new LkiFeed();

    require_once dirname(__FILE__) . '/views/settings.php';

    if (isset($_GET['code'])) {

    	$code = $_GET['code'];
    	$token = $lkifeed->getfeedApi()->getToken($code);
    	
    	$_SESSION['auth_token'] = $token;
    }
}

function lkiUpdateOptions($post) {

    if (!$post['lki_client_id'] || !$post['lki_client_secret'] || !$post['lki_client_redirect']) {
        throw new \Exception('Must have the client id, client secret and client redirect');
    } 

    update_option('lki_client_id', (sanitize_text_field(trim($post['lki_client_id']))));
    update_option('lki_client_secret', (sanitize_text_field(trim($post['lki_client_secret']))));
    update_option('lki_client_redirect', (sanitize_text_field(trim($post['lki_client_redirect']))));
}

function bootSession() {
	session_start();
}

add_action('wp_loaded', 'bootSession');

function lkiShortCode($atts, $content = null) {
	$atts = shortcode_atts([
		'url' => 'https://api.linkedin.com/v1/people/~?format=json',
	], $atts, 'lkifeed');

	try {
		$lkiFeed = new LkiFeed();
		$authToken = $_SESSION['auth_token'];
		$profile = $lkiFeed->getfeedApi()->getProfileInfo($atts['url'], $authToken);

		include dirname(__FILE__).'/views/shortcode.php';

	} catch (Exception $e) {
		return '<p class="error">' . $e->getMessage() . '</p>';
	}
}

add_shortcode('lki_feed', 'lkiShortCode');


