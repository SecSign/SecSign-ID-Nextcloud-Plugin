<?php

namespace OCA\SecSignID\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;

class PersonalSettings implements IPersonalProviderSettings {

	/**
	 * @return TemplateResponse
	 */
	public function getBody() {
		return new TemplateResponse('secsignid', 'settings/setup');
	}
}
