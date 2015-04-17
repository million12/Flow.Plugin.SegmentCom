<?php
namespace M12\SegmentCom;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "M12.SegmentCom".        *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class Segment
 * Wrapper to \Segment class in Testing environment
 *
 * @Flow\Scope("singleton")
 */
class SegmentTesting implements SegmentInterface {

	/**
	 * Initializes the default client to use. Uses the socket consumer by default.
	 *
	 * @param  string $secret  your project's secret key
	 * @param  array  $options passed straight to the client
	 */
	public function init($secret, $options = array()) {
		// nothing to do in Testing environment
	}

	/**
	 * Tracks a user action
	 *
	 * @param  array $message
	 * @return boolean whether the track call succeeded
	 */
	public function track(array $message) {
		// nothing to do in Testing environment
		return TRUE;
	}

	/**
	 * Tracks a page view
	 *
	 * @param  array $message
	 * @return boolean whether the page call succeeded
	 */
	public function page(array $message) {
		// nothing to do in Testing environment
		return TRUE;
	}

	/**
	 * Tags traits about the user.
	 *
	 * @param  array $message
	 * @return boolean whether the identify call succeeded
	 */
	public function identify(array $message) {
		// nothing to do in Testing environment
		return TRUE;
	}

	/**
	 * Flush the client
	 */
	public function flush() {
		// nothing to do in Testing environment
	}
}
