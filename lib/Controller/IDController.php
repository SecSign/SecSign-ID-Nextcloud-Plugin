<?php
namespace OCA\SecSignID\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

class SettingsController extends Controller {
	private $userId;
	private $mapper;

	public function __construct($AppName, IRequest $request, IDMapper $mapper, $UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->mapper = $mapper;
	}

	
	public function index() {
		return new DataResponse($this->mapper->findAll())  // templates/index.php
	}

	public function create($secsignid, $enabled){
		$id = new ID();
		$id->setUserId($this->userId);
		$id->setSecsignid($secsignid);
		$id->setEnabled($enabled);
		return new DataResponse($this->mapper->insert($id));
	}

	public function update($userId,$enabled){
		try{
			$id = $this->mapper->find($userId);
		} catch(Exception $e){
			return new DataResponse([,Http:STATUS_NOT_FOUND]);
		}
		$id->setEnabled($enabled);
		return new DataResponse($this->mapper->update($id));
	}

}