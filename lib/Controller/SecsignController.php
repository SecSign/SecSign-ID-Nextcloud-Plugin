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
use OCP\ISession;
use OCA\SecSignID\Service\AuthSession;

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

	private $session;

	public function __construct($AppName, IRequest $request,		
								$UserId, IAPI $iapi,
								IDMapper $mapper, PermissionService $permission, ISession $session){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iapi = $iapi;
		$this->mapper = $mapper;
		$this->permission = $permission;
		$this->session = $session;
	}

	/**
     * @NoAdminRequired
     * @PublicPage
     */
	public function state(){
		$accepted = $this->iapi->isSessionAccepted();
		return array('accepted' => $accepted);
	}

	/**
     * @NoAdminRequired
     * @PublicPage
	 * 
	 * @param array $session 
     */
	public function sessionState($session){
		return $this->iapi->getAuthState($session);
	}

	/**
     * @NoAdminRequired
     * @PublicPage
	 * @UseSession
     */
	public function idExists(){
		$secsignid = $this->getID();
		return $this->givenIdExists($secsignid);
	}

	/**
     * @NoAdminRequired
	 * @UseSession
	 * @param string $secsignid
     */
	public function givenIdExists($secsignid){
		$values = $this->iapi->idExists($secsignid);
		if(!empty($values['session'])){
			$this->session['session'] = $values['session'];
		}
		return $values;
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoAdminRequired
     * @PublicPage
	 */
	public function cancel(){
		$this->iapi->cancelAuthSession();
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoAdminRequired
     * @PublicPage
	 * 
	 * @param array $session
	 */
	public function cancelSession($session){
		$this->iapi->cancelSession($session);
	}

	/**
	 * Return the current secsignid
	 * 
	 * @NoAdminRequired
     * @PublicPage
	 */
	public function getID(){
		$current = $this->mapper->find($this->userId);
		if(isset($current)){
			return $current->getSecsignid();
		}else{
			return $this->userId . "@" . $this->permission->getAppValue("onboarding_suffix","test");
		}		
	}
}
