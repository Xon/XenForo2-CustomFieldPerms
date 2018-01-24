<?php

namespace SV\CustomFieldPerms\XF\Pub\Controller;

class Common {

	/**
	 * Insert additionalFilters into a reply.
	 * These are then passed in to the custom_fields_macro via a template modification.
	 *
	 * @param $reply
	 * @param $key
	 */
	public static function applyUsergroupCustomFieldPermissionFilters(&$reply, $key, $supplementaryFilters=[]) {
		if ($reply instanceof \XF\Mvc\Reply\View) {
			$visitorUserGroups = array_merge(
				array(\XF::visitor()->user_group_id),
				\XF::visitor()->secondary_group_ids
			);
			$reply->setParam(
				'additionalFilters',
				array(
					'check_usergroup_perms' => array(
						$visitorUserGroups, $key
					)
				) + $supplementaryFilters
			);
		}
	}
}
