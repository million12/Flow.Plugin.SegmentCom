<?php
namespace M12\SegmentCom\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "M12.SegmentCom".        *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use Segment;
use TYPO3\Party\Domain\Model\Person;

/**
 * Class SegmentService
 * Wrapper to \Segment class
 *
 * @Flow\Scope("singleton")
 */
class SegmentService {

	/**
	 * @Flow\Inject()
	 * @var \M12\SegmentCom\Service\SecurityService
	 */
	protected $securityService;

	/**
	 * Flag indicating that SegmentCom has been initialised
	 * 
	 * @var bool
	 */
	protected $alreadyInitialized = FALSE;

	/**
	 * Ts.Tracking settings
	 * 
	 * @Flow\InjectConfiguration(package="M12.SegmentCom")
	 * @var array
	 */
	protected $settings;

	/**
	 * Initialize / configure Segment
	 */
	protected function init() {
		if (TRUE === $this->alreadyInitialized) {
			return;
		}
		
		Segment::init($this->settings['writeKey'], $this->settings['clientOptions']);
		$this->alreadyInitialized = TRUE;
	}

	/**
	 * Flush / send the queue
	 * 
	 * @return void
	 */
	public function flush() {
		Segment::flush();
	}

	/**
	 * Flush/send the queue, but only if needed.
	 * 
	 * We flush it explicitly only in CLI mode. This is because we might have
	 * long-running PHP process (i.e. AMQP consumer) and then Segment
	 * won't flush the queue until it's full. We don't want to wait for so long
	 * so therefore we send it instantly.
	 */
	public function flushIfNeeded() {
		if (TRUE === $this->settings['flushImmediatelyWhenInCLI'] && 'cli' === php_sapi_name()) {
			$this->flush();
		}
	}

	/**
	 * Tracks a user action
	 *
	 * @param  array $message
	 * @param  Person $overrideUser
	 * @return boolean whether the track call succeeded
	 */
	public function track(array $message, Person $overrideUser = NULL) {
		$this->init();
		
		$this->insertContext($message);
		$this->insertUserIdOrAnonymousId($message, $overrideUser);
		
		$res = Segment::track($message);
		
		$this->flushIfNeeded();
		return $res;
	}

	/**
	 * Tags traits about the user.
	 * If 'userId' is not present in $message, will be automatically added
	 * from currently logged in user (if present)
	 *
	 * @param  array  $message
	 * @param  Person $overrideUser
	 * @return boolean whether the identify call succeeded
	 */
	public function identify(array $message = [], Person $overrideUser = NULL) {
		// no user? skip identify() call
		$user = $overrideUser ? $overrideUser : $this->securityService->getAuthenticatedParty();
		if (empty($user)) {
			return FALSE;
		}
		
		$this->init();
		
		$this->insertContext($message);
		$this->insertUserIdOrAnonymousId($message, $overrideUser);
		$this->insertUserTraits($message, $overrideUser);
		
		$res = Segment::identify($message);
		
		$this->flushIfNeeded();
		return $res;
	}

	/**
	 * Inject some context data, if available
	 * 
	 * @param array $message
	 */
	protected function insertContext(array &$message) {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$message['context']['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
		}
	}

	/**
	 * Inject authenticated userId into $message (if there's currently authenticated user).
	 * Otherwise it injects 
	 * 
	 * @param array $message
	 * @param Person $overrideUser
	 * @return void
	 */
	protected function insertUserIdOrAnonymousId(array &$message, $overrideUser = NULL) {
		if (empty($message['userId']) && empty($message['anonymousId'])) {
			$user = $overrideUser ? $overrideUser : $this->securityService->getAuthenticatedParty();
			$userId = $user ? ObjectAccess::getProperty($user, 'Persistence_Object_Identifier', TRUE) : NULL;
			
			if ($userId) {
				$message['userId'] = $userId;
			} else {
				$message['anonymousId'] = $this->securityService->getAnonymousId();
			}
		}
	}

	/**
	 * @param array $message
	 * @param Person $overrideUser
	 * @return void
	 */
	protected function insertUserTraits(array &$message, $overrideUser = NULL) {
		$user = $overrideUser ? $overrideUser : $this->securityService->getAuthenticatedParty();
		if (!$user) return;
		
		/** @var \TYPO3\Flow\Security\Account $account */
		$account = $user->getAccounts()->first();
		
		/** @var \TYPO3\Party\Domain\Model\PersonName $name */
		$name = $user->getName();
		
		$message['traits'] = [
			'email' => $account->getAccountIdentifier(),
			'username' => $account->getAccountIdentifier(),
			'firstName' => $name ? $name->getFirstName() : NULL,
			'lastName' => $name ? $name->getLastName() : NULL,
			'createdAt' => $account->getCreationDate()->format('c'),
		];
	}
}
