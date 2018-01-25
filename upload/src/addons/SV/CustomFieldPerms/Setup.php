<?php

namespace SV\CustomFieldPerms;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public static $tables1 = array(
		'xf_user_field' => array(
			'sedo_perms_input_enable'		=> array('type'=>'tinyint unsigned', 'default'=>0, 'entity_type'=>\XF\Mvc\Entity\Entity::UINT, 'entity_default'=>0, 'field_type'=>'uint'),
			'sedo_perms_output_pp_enable'	=> array('type'=>'tinyint unsigned', 'default'=>0, 'entity_type'=>\XF\Mvc\Entity\Entity::UINT, 'entity_default'=>0, 'field_type'=>'uint'),
			'sedo_perms_output_ui_enable'	=> array('type'=>'tinyint unsigned', 'default'=>0, 'entity_type'=>\XF\Mvc\Entity\Entity::UINT, 'entity_default'=>0, 'field_type'=>'uint'),
			'sedo_perms_input_val'			=> array('type'=>'blob', 'entity_type'=>\XF\Mvc\Entity\Entity::SERIALIZED, 'entity_default'=>'', 'field_type'=>'array'),
			'sedo_perms_output_pp_val'		=> array('type'=>'blob', 'entity_type'=>\XF\Mvc\Entity\Entity::SERIALIZED, 'entity_default'=>'', 'field_type'=>'array'),
			'sedo_perms_output_ui_val'		=> array('type'=>'blob', 'entity_type'=>\XF\Mvc\Entity\Entity::SERIALIZED, 'entity_default'=>'', 'field_type'=>'array'),
		),
	);

	public function installStep1()
	{
		foreach (self::$tables1 as $table=>$columns) {
			$this->schemaManager()->alterTable($table, function(Alter $table) use ($columns)
			{
				foreach ($columns as $column => $details) {
					$col = $table->addColumn($column, $details['type']);
					if (array_key_exists('default', $details)) {
						$col->setDefault($details['default']);
					}
				}
			});
		}

	}

	public function uninstallStep1() {
		foreach (self::$tables1 as $table=>$columns) {
			$this->schemaManager()->alterTable($table, function (Alter $table) use ($columns) {
				$table->dropColumns(array_keys($columns));
			});
		}
	}
}