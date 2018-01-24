<?php

namespace SV\CustomFieldPerms\XF\Admin\Controller;

use SV\CustomFieldPerms\Setup;
use XF\Mvc\FormAction;

class UserField extends XFCP_UserField
{

	/**
	 * Insert additional data into the field regarding permissions.
	 * @param \XF\Entity\AbstractField $field
	 *
	 * @return \XF\Mvc\Reply\View
	 */
	protected function fieldAddEditResponse(\XF\Entity\AbstractField $field)
	{
		$reply = parent::fieldAddEditResponse($field);

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			// get list of usergroups including an "all"
			/** @var \XF\Repository\UserGroup $ugRepo */
			$ugRepo = $this->repository('XF:UserGroup');
			$userGroups = $ugRepo->findUserGroupsForList()->fetchColumns('user_group_id', 'title');
			array_unshift($userGroups, ['title' => 'all', 'user_group_id' => 'all']);

			// get the permission value keys, and associated permissions
			$permValKeys = array_filter(array_keys(Setup::$tables1['xf_user_field']), function ($a) {
				return preg_match('/^.*val$/', $a);
			});
			$permVals = array_map(function($key) use ($reply) {
				return $reply->getParams()['field'][$key];
			}, $permValKeys);

			// insert permission sets into the field
			array_map(
				// permission sets
				function($permValKey, $permVal) use ($userGroups, $field) {
					// usergroups in those permission sets
					$field->set($permValKey, array_map(function($userGroup) use ($permVal){
						return [
							'selected' => !empty($permVal) ? in_array($userGroup['user_group_id'], $permVal) : null,
							'value' => $userGroup['user_group_id'],
							'label' => filter_var($userGroup['title'], FILTER_SANITIZE_STRING),
						];
					}, $userGroups));
				},
				$permValKeys, $permVals
			);

			$reply->setParam('field',$field);
		}

		return $reply;
	}

	/**
	 * Save the new permission fields that are included in the form.
	 * @param FormAction $form
	 * @param \XF\Entity\AbstractField $field
	 *
	 * @return void|FormAction
	 */
	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractField $field)
	{
		$form = parent::saveAdditionalData($form, $field);

		$elements = [];
		foreach (Setup::$tables1['xf_user_field'] as $column => $details) {
			$elements[ $column ] = $details['field_type'];
		}

		$input = $this->filter($elements);

		$form->basicEntitySave($field, $input);

		return $form;
	}
}