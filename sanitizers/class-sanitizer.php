<?php
/**
 * Sanitizer
 *
 * @package Google\AMP_Plugin_Name_Compat
 */

namespace Google\AMP_One_Signal_Compat;

use AMP_Base_Sanitizer;
use DOMElement;
use DOMXPath;

/**
 * Class Sanitizer
 */
class Sanitizer extends AMP_Base_Sanitizer {

	/**
	 * Sanitize.
	 */
	public function sanitize() {
		$xpath = new DOMXPath( $this->dom );

		// Remove Inline Script added by one signal plugin.
		$scripts = $xpath->query( '//script[contains(., "OneSignal")]' );

		if ( $scripts instanceof \DOMNodeList ) {
			foreach ( $scripts as $script ) {
				if ( $script instanceof \DOMElement ) {
					$script->parentNode->removeChild( $script );
				}
			}
		}

	}

}
