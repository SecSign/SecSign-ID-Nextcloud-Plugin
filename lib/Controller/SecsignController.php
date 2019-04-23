<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUser;

use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;

use OCA\SecSignID\Service\PermissionService;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class SecsignController extends Controller {
	private $userId;

	/** @var IAPI */
	private $iapi;

	private $mapper;

	private $permission;

	public function __construct($AppName, IRequest $request,		
								$UserId, IAPI $iapi,
								IDMapper $mapper, PermissionService $permission){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
		$this->permission = $permission;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function state(){
		$accepted = $this->iapi->isSessionAccepted();
		return array('accepted' => $accepted);
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 * 
	 * @param array $session 
     */
	public function sessionState($session){
		return $this->iapi->getAuthState($session);
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function idExists(){
		$secsignid = $this->getID();
		return $this->iapi->idExists($secsignid);
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 */
	public function cancel(){
		$this->iapi->cancelAuthSession();
	}

	/**
	 * Return the current secsignid
	 * 
	 * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 */
	public function getID(){
		return $this->userId . "@" . $this->permission->getAppValue("onboarding_suffix","test");
	}
}
