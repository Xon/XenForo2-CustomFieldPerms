<?php

namespace SV\CustomFieldPerms\XF\Pub\Controller;

use SV\CustomFieldPerms\XF\Repository\UserField;
use XF\Mvc\ParameterBag;

class Member extends XFCP_Member {
	public function actionAbout(ParameterBag $params)
	{
		$reply = parent::actionAbout($params);

		/** @var \SV\CustomFieldPerms\XF\Repository\UserField $repo */
		$repo = \XF::repository('XF:UserField');
		$repo->applyUsergroupCustomFieldPermissionFilters(
			$reply,
			'sedo_perms_output_pp',
			['profile']
		);

		return $reply;
	}
}