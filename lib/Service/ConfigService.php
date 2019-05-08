<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Service;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\Template;

use OCP\AppFramework\Controller;
use OCA\SecSignID\Service\PermissionService;
use OCA\SecSignID\Service\QRCode;
use OCA\SecSignID\Exceptions\InvalidInputException;

class ConfigService {
    private $userId;

	private $manager;

	private $permissions;

    private $groupmanager;
    

    public function __construct($AppName, IRequest $request,		
								$UserId, IUserManager $manager, PermissionService $permission, IGroupManager $groupmanager){
		$this->userId = $UserId;
		$this->manager = $manager;
		$this->permissions = $permission;
		$this->groupmanager = $groupmanager;
    }
    
    /**
	 * Saves changes to server address
     * 
	 * @param array $address
	 */
	public function saveServer($server){
		if(!empty($server)){
			if(!empty($server['server'])){
				$this->permissions->setAppValue("server",$server["server"]);
			}
			if(!empty($server['fallback'])){
				$this->permissions->setAppValue("fallback",$server["fallback"]);
			}
			if(!empty($server['serverport'])){
				$this->permissions->setAppValue("serverport",$server["serverport"]);
			}
			if(!empty($server['fallbackport'])){
				$this->permissions->setAppValue("fallbackport",$server["fallbackport"]);
			}
		}else{
            throw new InvalidInputException("Server data cannot be empty. Please try again.");
        }
	}

	/**
	 * Saves changes to mobile server address
     * 
	 * @param string $server
	 */
	public function saveServerMobile($server){
		if(!empty($server)){
			$this->permissions->setAppValue("mobileurl", $server);
		}else{
            throw new InvalidInputException("Server address cannot be empty. Please try again");
        }
	}

	/**
	 * Gets server data
	 */
	public function getServer(){
		return [
			server => (string) $this->permissions->getAppValue("server", "https://httpapi.secsign.com"),
			fallback => (string) $this->permissions->getAppValue("fallback", "https://httpapi2.secsign.com"),
			serverport => (int) $this->permissions->getAppValue("serverport", 443),
			fallbackport => (int) $this->permissions->getAppValue("fallbackport", 443)
		];
	}

	/**
	 * Gets mobile server data
	 */
	public function getServerMobile(){
		return (string) $this->permissions->getAppValue("mobileurl", "id1.secsign.com");
	}


	/**
	 * Gets QR code for new SecSignID
	 */
	public function getQR(){
		$secsignid = $this->userId . "@" . $this->permissions->getAppValue("onboarding_suffix","test");		
		$serverurl = $this->permissions->getAppValue("mobileurl","id1.secsign.com");
		$uri =  "com.secsign.secsignid://create?idserverurl=".$serverurl."&secsignid=". $secsignid;
		QRCode::png($uri);
	}

	/**
	 * Gets QR code for given secsignid.
	 * 
	 * @param string $secsignid
	 */
	public function getQRForId($secsignid){
		$serverurl = $this->permissions->getAppValue("mobileurl", "id1.secsign.com");
		$uri =  "com.secsign.secsignid://create?idserverurl=".$serverurl."&secsignid=". $secsignid;
		QRCode::png($uri);
	}

	/**
	 * Allows users to edit the settings of their SecSign 2FA
	 * 
	 * 
	 * @param boolean $allow
	 */
	public function allowUserEdit($allow){
        $this->permissions->setAppValue("allowEdit", $allow);
    }

    /**
	 * Gets status of editing permissions for all users.
	 * 
	 */
	public function getAllowUserEdit(){
		return $this->permissions->getAppValue("allowEdit", false);
	}


	/**
	 * Gets status of editing permissions for current user. Always returns true if user is an admin.
	 * 
	 * @NoAdminRequired
	 */
	public function canUserEdit(){
		$user = $this->manager->get($this->userId);
		$groups = $this->groupmanager->getUserGroups($user);
		foreach ($groups as &$group){
			if($group->getGID() === "admin"){
				return true;
			}
		}
		return $this->permissions->getAppValue("allowEdit", false);
	}

	/**
	 * Gets the status of user onbaording
	 */
	public function getOnboarding(){
		return [
			enabled => $this->permissions->getAppValue("onboarding_enabled", false),
			suffix => $this->permissions->getAppValue("onboarding_suffix", "")
		];
	}

	/**
	 * Changes the status of user onbaording
	 * 
	 * @param array data
	 */
	public function changeOnboarding($data){
        if(!empty($data)){
            if(isset($data['enabled'])){
                $enabled = $data["enabled"] === "true" ? true : false;
                if(isset($data['suffix'])){
                    $suffix = $data["suffix"];
                }else{
                    $suffix = '';
                }
                
            }else{
                throw new InvalidInputException('Input was not of the correct form. Please try again');
            }
        }else{
            throw new InvalidInputException('Input was empty. Please try again.');
        }		
		
		$this->permissions->setAppValue("onboarding_enabled", $enabled);
        $this->permissions->setAppValue("onboarding_suffix", $suffix);
        return true;
    }
}