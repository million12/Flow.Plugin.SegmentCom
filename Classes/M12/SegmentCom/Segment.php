<?php
namespace M12\SegmentCom;

use TYPO3\Flow\Annotations as Flow;

/**
 * Class Segment
 * Wrapper to \Segment class
 *
 * @Flow\Scope("singleton")
 */
class Segment implements SegmentInterface
{

    /**
     * Initializes the default client to use. Uses the socket consumer by default.
     *
     * @param  string $secret your project's secret key
     * @param  array $options passed straight to the client
     */
    public function init($secret, $options = array())
    {
        \Segment::init($secret, $options);
    }

    /**
     * Tracks a user action
     *
     * @param  array $message
     * @return boolean whether the track call succeeded
     */
    public function track(array $message)
    {
        return \Segment::track($message);
    }

    /**
     * Tracks a page view
     *
     * @param  array $message
     * @return boolean whether the page call succeeded
     */
    public function page(array $message)
    {
        return \Segment::page($message);
    }

    /**
     * Tags traits about the user.
     *
     * @param  array $message
     * @return boolean whether the identify call succeeded
     */
    public function identify(array $message)
    {
        return \Segment::identify($message);
    }

    /**
     * Flush the client
     */
    public function flush()
    {
        \Segment::flush();
    }
}
