<?php

namespace SV\CustomFieldPerms\XF\Repository;

class UserField extends XFCP_UserField {

	public function rebuildUserFieldValuesCache($userId)
	{
		$result = parent::rebuildUserFieldValuesCache($userId);

		$fieldPerms = $this->finder('XF:UserField')
			->fetchColumns('field_id','sedo_perms_output_ui_enable','sedo_perms_output_ui_val');

		$permsCache = [];
		foreach ($fieldPerms as $fieldPerm) {
			if ($fieldPerm['sedo_perms_output_ui_enable']) {
				$permsCache[ $fieldPerm['field_id'] ] = $fieldPerm['sedo_perms_output_ui_val'];
			}
		}

		\XF::registry()->set('sedo_perms_ui_users', $permsCache);

		return $result;
	}

	/**
	 * Insert additionalFilters into a reply.
	 * These are then passed in to the custom_fields_macro via a template modification.
	 *
	 * @param $reply
	 * @param $key
	 */
	public function applyUsergroupCustomFieldPermissionFilters(&$reply, $key, $supplementaryFilters=[]) {
		if ($reply instanceof \XF\Mvc\Reply\View) {
			$visitorUserGroups = array_merge(
				array(\XF::visitor()->user_group_id),
				\XF::visitor()->secondary_group_ids
			);
			$reply->setParam(
				'additionalFilters',
				array_merge(
					array(
						'check_usergroup_perms' => array(
							$visitorUserGroups, $key
						)
					),
					$supplementaryFilters
				)
			);
		}
	}

}