<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use SV\StandardLib\Helper;
use XF\Entity\AbstractField;
use XF\Mvc\FormAction;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\View as ViewReply;
use XF\Repository\UserGroup as UserGroupRepo;
use function array_filter;
use function array_keys;
use function array_map;
use function array_unshift;
use function in_array;
use function is_array;
use function preg_match;

trait CustomFieldAdminTrait
{
    /**
     * Insert additional data into the field regarding permissions.
     *
     * @param AbstractField $field
     * @return AbstractReply
     */
    protected function fieldAddEditResponse(AbstractField $field)
    {
        $reply = parent::fieldAddEditResponse($field);

        if ($reply instanceof ViewReply)
        {
            // get list of usergroups including an "all"
            $ugRepo = Helper::repository(UserGroupRepo::class);
            $userGroups = $ugRepo->findUserGroupsForList()->fetchColumns('user_group_id', 'title');
            array_unshift($userGroups, ['title' => 'all', 'user_group_id' => 'all']);

            // get the permission value keys, and associated permissions
            $entityClassName = $this->getClassIdentifier();
            $structure = Helper::getEntityStructure($entityClassName);
            $table = Globals::$tables[$structure->table] ?? null;
            if ($table !== null)
            {
                $permValKeys = array_filter(
                    array_keys($table), function ($a) {
                    return preg_match('/^cfp_.*_val$/', $a);
                });
                $field = $reply->getParam('field') ?? [];
                $permVals = array_map(function ($key) use ($field) {
                    return $field[$key] ?? [];
                }, $permValKeys);
                // $permVals is an array of group ids, and/or the string 'all'

                // insert permission sets into the field
                array_map(
                // permission sets
                    function ($permValKey, $permVal) use ($userGroups, $field) {
                        // usergroups in those permission sets
                        $field->set(
                            $permValKey, array_map(
                                function ($userGroup) use ($permVal) {
                                    return [
                                        'selected' => is_array($permVal) && in_array((string)$userGroup['user_group_id'], $permVal, true),
                                        'value'    => $userGroup['user_group_id'],
                                        'label'    => $userGroup['title'],
                                    ];
                                }, $userGroups
                            )
                        );
                    },
                    $permValKeys, $permVals
                );

                $reply->setParam('field', $field);
            }
        }

        return $reply;
    }

    /**
     * Save the new permission fields that are included in the form.
     *
     * @param FormAction               $form
     * @param AbstractField $field
     * @return FormAction
     */
    protected function saveAdditionalData(FormAction $form, AbstractField $field)
    {
        $form = parent::saveAdditionalData($form, $field);

        $elements = [];
        $entityClassName = $this->getClassIdentifier();
        $structure = Helper::getEntityStructure($entityClassName);
        $table = Globals::$tables[$structure->table] ?? null;
        if (is_array($table))
        {
            foreach ($table as $column => $details)
            {
                $elements[$column] = $details['field_type'];
            }

            $input = $this->filter($elements);

            $form->basicEntitySave($field, $input);
        }

        return $form;
    }
}