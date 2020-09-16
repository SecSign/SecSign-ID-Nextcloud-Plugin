<?php
/**
 * @author SecSign Technologies Inc.
 * @copyright 2019 SecSign Technologies Inc.
 */
namespace OCA\SecSignID\Provider;


use OCP\Template;
use OCP\Authentication\TwoFactorAuth\ILoginSetupProvider;


/**
 * SecSign2FA is starts an authentication session once a user has
 * entered a correct username password combination.
 */
class LoginSetupProvider implements ILoginSetupProvider {

	private $userId;

	public function getBody(): Template{
        return new Template('secsignid', 'login/enrollment');
    }
}