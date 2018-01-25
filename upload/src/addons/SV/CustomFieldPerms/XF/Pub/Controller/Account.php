<?php

namespace SV\CustomFieldPerms\XF\Pub\Controller;

class Account extends XFCP_Account {

	/**
	 * Add new filter types for the preferences page.
	 * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
	 */
	public function actionPreferences()
	{
		$reply = parent::actionPreferences();

		/** @var \SV\CustomFieldPerms\XF\Repository\UserField $repo */
		$repo = \XF::repository('XF:UserField');
		$repo->applyUsergroupCustomFieldPermissionFilters($reply, 'sedo_perms_input');

		return $reply;
	}

	/**
	 * Add new filter types for the account details page.
	 * @return \XF\Mvc\Reply\Redirect|\XF\Mvc\Reply\View
	 */
	public function actionAccountDetails()
	{
		$reply = parent::actionAccountDetails();

		/** @var \SV\CustomFieldPerms\XF\Repository\UserField $repo */
		$repo = \XF::repository('XF:UserField');
		$repo->applyUsergroupCustomFieldPermissionFilters($reply, 'sedo_perms_input');

		return $reply;
	}

}
