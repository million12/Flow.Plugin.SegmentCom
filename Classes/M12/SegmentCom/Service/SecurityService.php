<?php
namespace M12\SegmentCom\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "M12.SegmentCom".        *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * Class SecurityService
 *
 * @Flow\Scope("singleton")
 */
class SecurityService {

	/**
	 * @Flow\Inject()
	 * @var \TYPO3\Party\Domain\Service\PartyService
	 */
	protected $partyService;

	/**
	 * @Flow\Inject()
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject()
	 * @var AccountRepository
	 */
	protected $accountRepository;
	
	
	
	/**
	 * Get currently authenticated user
	 *
	 * @return AbstractParty|NULL
	 */
	public function getAuthenticatedParty() {
		if (($account = $this->securityContext->getAccount())) {
			return $this->partyService->getAssignedPartyOfAccount($account);
		}
		return NULL;
	}

	/**
	 * Find Party by its account identifier
	 *
	 * @param string $accountIdentifier : account identifier
	 * @param string $authenticationProviderName
	 * @return NULL|AbstractParty
	 */
	public function getPartyByAccountIdentifier($accountIdentifier, $authenticationProviderName = 'DefaultProvider') {
		if (empty($accountIdentifier)) {
			return NULL;
		}
		
		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($accountIdentifier, $authenticationProviderName);
		if ($account) {
			return $this->partyService->getAssignedPartyOfAccount($account);
		}
		return NULL;
	}
	
	/**
	 * Get anonymous ID (from security context hash).
	 * Used when no authenticated user UUID is present.
	 * 
	 * @return string
	 */
	public function getAnonymousId() {
		return $this->securityContext->getContextHash();
	}
}
