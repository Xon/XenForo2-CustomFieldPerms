<?php

namespace SV\CustomFieldPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Member extends XFCP_Member {
	public function actionAbout(ParameterBag $params)
	{
		$reply = parent::actionAbout($params);

		Common::applyUsergroupCustomFieldPermissionFilters($reply, 'sedo_perms_output_pp', ['profile']);

		return $reply;
	}
}