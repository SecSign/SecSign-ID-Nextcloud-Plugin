<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUser;

use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\IAPI;
use OCA\SecSignID\Db\IDMapper;
use OCA\SecSignID\Db\ID;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class SecsignController extends Controller {
	private $userId;

	/** @var IAPI */
	private $iapi;

	private $mapper;

	public function __construct($AppName, IRequest $request,		
								$UserId, IAPI $iapi,
								IDMapper $mapper){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
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
	 * Cancels the pending authsession-.
	 * 
	 * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 */
	public function cancel(){
		$this->iapi->cancelAuthSession();
	}

}
