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

}