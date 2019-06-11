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
				$port = (int) $server['serverport'];
				if($port < 65535 && $port > 0){
					$this->permissions->setAppValue("serverport", $port);
				}else{
					throw new InvalidInputException("Port number must be between 0 and 65535");
				}
			}
			if(!empty($server['fallbackport'])){
				$port = (int) $server["fallbackport"];
				if($port < 65535 && $port > 0){
					$this->permissions->setAppValue("fallbackport", $port);
				}else{
					throw new InvalidInputException("Port number must be between 0 and 65535");
				}
			}
			return true;
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
			return True;
		}else{
            throw new InvalidInputException("Server address cannot be empty. Please try again");
        }
	}

	/**
	 * Gets server data
	 */
	public function getServer(){
		$server = (string) $this->permissions->getAppValue("server", "https://httpapi.secsign.com");
		$fallback = (string) $this->permissions->getAppValue("fallback", "https://httpapi2.secsign.com");
		$serverport = (int) $this->permissions->getAppValue("serverport", 443);
		$fallbackport = (int) $this->permissions->getAppValue("fallbackport", 443);
		return [
			"server" => $server,
			"fallback" => $fallback,
			"serverport" => $serverport,
			"fallbackport" => $fallbackport
		];
	}

	/**
	 * Gets mobile server data
	 */
	public function getServerMobile(){
		return (string) $this->permissions->getAppValue("mobileurl", "id1.secsign.com");
	}

	/**
	 * Gets the settings for choosing an id during enrollment
	 */
	public function getIdChoiceAllowedForUsers(){
		$allowed = $this->permissions->getAppValue("choice_enabled", false);
		$groups = json_decode($this->permissions->getAppValue("choice_groups", json_encode([])));
		return [
			"allowed" => $allowed,
			"groups" => $groups
		];
	}

	/**
	 * Gets the settings for choosing an id during enrollment for current user
	 */
	public function getIdChoiceAllowed(){
		$enabled = $this->permissions->getAppValue("choice_enabled", false);
		$groups = json_decode($this->permissions->getAppValue("choice_groups", json_encode([])));
		$user = $this->manager->get($this->userId);
		$usergroups = $this->groupmanager->getUserGroupIds($user);
		$groupAllowed = array_intersect($groups, $usergroups) or empty($groups);
		return $enabled; //and $groupAllowed;
	}

	/**
	 * Saves settings for id choice during enrollement
	 * 
	 * @param string $allowed
	 * @param array $groups
	 */
	public function setIdChoiceAllowed($allowed, $groups){
		$enable = $allowed === "true" ? true : false;
		$this->permissions->setAppValue("choice_enabled", $enable);
		$this->permissions->setAppValue("choice_groups", json_encode($groups));
	}


	/**
	 * Gets QR code for new SecSignID
	 */
	public function getQR(){
		$secsignid = $this->userId . "@" . $this->permissions->getAppValue("onboarding_suffix","test");		
		return getQRForId($secsignid);
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
	 * @param array $data
	 */
	public function allowUserEdit($data){
		$allow = $data['allow']  === "true" ? true : false;
		$allow_groups= $data['allowGroups'] === "true" ? true : false;
		$allow_for = json_encode($data['groups']);
		$this->permissions->setAppValue("allowEdit", $allow);
		$this->permissions->setAppValue("allowGroups", $allow_groups);
		$this->permissions->setAppValue("allowEditGroups", $allow_for);
    }

    /**
	 * Gets status of editing permissions for all users.
	 * 
	 */
	public function getAllowUserEdit(){
		$allow = $this->permissions->getAppValue("allowEdit", false);
		$allow_groups = $this->permissions->getAppValue("allowGroups", false);
		$groups = json_decode($this->permissions->getAppValue("allowEditGroups", '{}'));
		$data = [
			'allow' => $allow,
			'allowGroups' => $allow_groups,
			'groups' => $groups
		];
		return $data;
	}


	/**
	 * Gets status of editing permissions for current user.
	 * 
	 * @NoAdminRequired
	 */
	public function canUserEdit(){
		$data = $this->getAllowUserEdit();
		$allow = $data['allow'];
		$allow_groups = $data['allowGroups'];
		if(!$allow_groups){
			return $allow;
		}else{
			$user = $this->manager->get($this->userId);
			$usergroups = $this->groupmanager->getUserGroupIds($user);
			foreach($data['groups'] as &$group){
				if($group == 'no group' && empty($usergroups)){
					return true;
				}else if(in_array($group, $usergroups)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Gets the status of user onbaording
	 */
	public function getOnboarding(){
		$choice = $this->getIdChoiceAllowedForUsers();
		return [
			"enabled" => $this->permissions->getAppValue("onboarding_enabled", false),
			"suffix" => $this->permissions->getAppValue("onboarding_suffix", ""),
			"allowed" => $choice["allowed"],
			"groups" => $choice["groups"]
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
		$allowed = $data["allowed"];
		$groups = $data["groups"];
		$this->setIdChoiceAllowed($allowed, $groups);
		$this->permissions->setAppValue("onboarding_enabled", $enabled);
        $this->permissions->setAppValue("onboarding_suffix", $suffix);
        return true;
    }
}