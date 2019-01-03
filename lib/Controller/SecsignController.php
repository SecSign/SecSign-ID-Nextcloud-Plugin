<?php
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\IAPI;

class SecsignController extends Controller {
	private $userId;
	private $iApi;

	public function __construct($AppName, IRequest $request, $UserId, IAPI $iApi){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->iApi = $iApi;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function state() : DataResponse{
		$accepted = $iApi->isSessionAccepted();
		if(!$accepted){
			return new DataResponse(array('data' => array('accepted' => $accepted),'status' => 'success'));	
		}else{
			return new DataResponse(array('data' => array('accepted' => $accepted),'status' => 'error'));
		}
		
	}

}
