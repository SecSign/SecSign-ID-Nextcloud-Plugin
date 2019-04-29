<?php
/**
 * This class links to the Users Template.
 * 
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;

use OCA\SecSignID\Db\IDMapper;

class PageController extends Controller {
	private $userId;
	private $secsignid;

	public function __construct($AppName, IRequest $request, $UserId, IDMapper $mapper){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->secsignid = $mapper->find($UserId);
	}

	/**
	 * @NoCSRFRequired
	 */
	public function index() {
		if(!isset($this->secsignid)){
			return new TemplateResponse('secsignid', 'content/setup_id_first');
		}else{
			return new TemplateResponse('secsignid', 'content/users');
		}		
	}

}
