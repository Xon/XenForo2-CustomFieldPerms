<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\Setup;

/*
 * Extends \XF\Entity\UserField
 */
class UserField extends XFCP_UserField
{

	/**
	 * Insert additional columns into UserField
	 *
	 * @param \XF\Mvc\Entity\Structure $structure
	 *
	 * @return \XF\Mvc\Entity\Structure
	 */
	public static function getStructure(\XF\Mvc\Entity\Structure $structure) {
		$structure = parent::getStructure($structure);

		foreach (Setup::$tables1['xf_user_field'] as $column => $details) {
			$structure->columns += [$column => ['type'=>$details['entity_type'], 'default' => $details['entity_default']]];
		}

		return $structure;
	}

}