<?php
namespace M12\SegmentCom;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "M12.SegmentCom".        *
 *                                                                        *
 *                                                                        */

interface SegmentInterface {

	/**
	 * Initializes the default client to use. Uses the socket consumer by default.
	 * @param  string $secret   your project's secret key
	 * @param  array  $options  passed straight to the client
	 */
	public function init($secret, $options = array());

	/**
	 * Tracks a user action
	 *
	 * @param  array $message
	 * @return boolean whether the track call succeeded
	 */
	public function track(array $message);

	/**
	 * Tracks a page view
	 *
	 * @param  array $message
	 * @return boolean whether the page call succeeded
	 */
	public function page(array $message);

	/**
	 * Tags traits about the user.
	 *
	 * @param  array  $message
	 * @return boolean whether the identify call succeeded
	 */
	public function identify(array $message);

	/**
	 * Flush the client
	 */
	public function flush();
}
