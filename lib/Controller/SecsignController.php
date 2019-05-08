<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCA\SecSignID\Service\SecsignService;

use OCP\AppFramework\Controller;

/**
 * The SecSignController links to required templates and handles requests to the server.
 */
class SecsignController extends Controller {
	private $userId;

	private $secsign;

	use Errors;

	public function __construct($AppName, IRequest $request, $UserId, SecSignService $secsign){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->secsign = $secsign;
	}

	/**
	 * @NoCSRFRequired
     * @NoAdminRequired
     * @PublicPage
	 * 
	 * @UserRateThrottle(limit=105, period=100)
     * @AnonRateThrottle(limit=105, period=100)
     */
	public function state(){
		return $this->handleSecsignException( function () {
			return $this->secsign->state();
		});	
	}

	/**
     * @NoAdminRequired
     * @PublicPage
	 * @NoCSRFRequired
	 * 
	 * @param array $session 
     */
	public function sessionState($session){
		return $this->handleSecsignException( function () use ($session){
			return $this->secsign->sessionState($session);
		});		
	}

	/**
     * @NoAdminRequired
     * @PublicPage
	 * @NoCSRFRequired
	 * @UseSession
     */
	public function idExists(){
		return $this->handleSecsignException( function () {
			return $this->secsign->idExists();
		});	
	}

	/**
     * @NoAdminRequired
	 * @UseSession
	 * @param string $secsignid
     */
	public function givenIdExists($secsignid){
		return $this->handleSecsignException( function () use ($secsignid) {
			return $this->secsign->givenIdExists($secsignid);
		});	
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoCSRFRequired
	 * @NoAdminRequired
     * @PublicPage
	 */
	public function cancel(){
		return $this->handleSecsignException( function () {
			return $this->secsign->cancel();
		});	
	}

	/**
	 * Cancels the pending authsession-.
	 * 
	 * @NoCSRFRequired
	 * @NoAdminRequired
     * @PublicPage
	 * 
	 * @param array $session
	 */
	public function cancelSession($session){
		return $this->handleSecsignException( function () use($session) {
			return $this->secsign->cancelSession($session);
		});	
	}

	/**
	 * Return the current secsignid
	 * 
	 * @NoCSRFRequired
	 * @NoAdminRequired
     * @PublicPage
	 */
	public function getID(){
		return $this->handleSecsignException( function () {
			return $this->secsign->getID();
		});		
	}
}
