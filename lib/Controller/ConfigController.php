<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\SecSignID\Service\ConfigService;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class ConfigController extends Controller {
	private $userId;

	private $config;

	use Errors;

	public function __construct($AppName, IRequest $request,		
								$UserId, ConfigService $config){
		parent::__construct($AppName, $request);
		$this->userId = $userId;
		$this->config = $config;
	}


	/**
	 * Saves changes to server address
	 * 
	 * 
	 * @param array $address
	 */
	public function saveServer($server){
		return $this->handleInvalidInput(function () use ($server){
			return $this->config->saveServer($server);
		});
	}

	/**
	 * Saves changes to mobile server address
	 * 
	 * 
	 * @param array $address
	 */
	public function saveServerMobile($server){
		return $this->handleInvalidInput(function () use ($server){
			return $this->config->saveServerMobile($server);
		});
	}

	/**
	 * Gets server data
	 * 
	 */
	public function getServer(){
		return $this->config->getServer();
	}

	/**
	 * Gets mobile server data
	 * 
	 * @NoCSRFRequired
	 */
	public function getServerMobile(){
		return $this->config->getServerMobile();
	}


	/**
	 * Gets QR code for new SecSignID
	 * 
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @UserRateThrottle(limit=5, period=100)
	 * @AnonRateThrottle(limit=3, period=100)
	 */
	public function getQR(){
		return $this->config->getQR();
	}

	/**
	 * Gets QR code for given secsignid.
	 * 
	 * @NoAdminRequired
	 * @UserRateThrottle(limit=5, period=100)
	 * 
	 * @param string $secsignid
	 */
	public function getQRForId($secsignid){
		return $this->handleInvalidInput(function () use ($secsignid){
			return $this->config->getQRForId($secsignid);
		});
	}

	/**
	 * Allows users to edit the settings of their SecSign 2FA
	 * 
	 * @param boolean $allow
	 */
	public function allowUserEdit($allow){
		return $this->handleInvalidInput(function () use ($allow){
			return $this->config->allowUserEdit($allow);
		});
    }

    /**
	 * Gets status of editing permissions for all users.
	 * 
	 */
	public function getAllowUserEdit(){
		return $this->config->getAllowUserEdit();
	}


	/**
	 * Gets status of editing permissions for current user. Always returns true if user is an admin.
	 * 
	 * @NoAdminRequired
	 */
	public function canUserEdit(){
		return $this->config->canUserEdit();
	}

	/**
	 * Gets the status of user onbaording
	 * 
	 * @NoAdminRequired
	 */
	public function getOnboarding(){
		return $this->config->getOnboarding();
	}

	/**
	 * Changes the status of user onbaording
	 * 
	 * 
	 * @param array data
	 */
	public function changeOnboarding($data){
		return $this->handleInvalidInput(function () use ($data){
			return $this->config->changeOnboarding($data);
		});
	}
}
