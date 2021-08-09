<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\Entity\AbstractField;
use XF\Mvc\FormAction;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\View;

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
        /** @noinspection PhpUndefinedClassInspection */
        $reply = parent::fieldAddEditResponse($field);

        if ($reply instanceof View)
        {
            // get list of usergroups including an "all"
            /** @var \XF\Repository\UserGroup $ugRepo */
            $ugRepo = $this->repository('XF:UserGroup');
            $userGroups = $ugRepo->findUserGroupsForList()->fetchColumns('user_group_id', 'title');
            array_unshift($userGroups, ['title' => 'all', 'user_group_id' => 'all']);

            // get the permission value keys, and associated permissions
            $entityClassName = $this->getClassIdentifier();
            $structure = \XF::em()->getEntityStructure($entityClassName);
            if (isset(Globals::$tables[$structure->table]))
            {
                $permValKeys = array_filter(
                    array_keys(Globals::$tables[$structure->table]), function ($a) {
                    return preg_match('/^cfp_.*_val$/', $a);
                }
                );
                $permVals = array_map(
                    function ($key) use ($reply) {
                        return $reply->getParams()['field'][$key];
                    }, $permValKeys
                );

                // insert permission sets into the field
                array_map(
                // permission sets
                    function ($permValKey, $permVal) use ($userGroups, $field) {
                        // usergroups in those permission sets
                        $field->set(
                            $permValKey, array_map(
                                function ($userGroup) use ($permVal) {
                                    return [
                                        'selected' => !empty($permVal) ? in_array($userGroup['user_group_id'], $permVal, true) : null,
                                        'value'    => $userGroup['user_group_id'],
                                        'label'    => filter_var($userGroup['title'], FILTER_SANITIZE_STRING),
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
        /** @noinspection PhpUndefinedClassInspection */
        $form = parent::saveAdditionalData($form, $field);

        $elements = [];
        $entityClassName = $this->getClassIdentifier();
        $structure = \XF::em()->getEntityStructure($entityClassName);
        if (isset(Globals::$tables[$structure->table]))
        {
            foreach (Globals::$tables[$structure->table] as $column => $details)
            {
                $elements[$column] = $details['field_type'];
            }

            $input = $this->filter($elements);

            $form->basicEntitySave($field, $input);
        }

        return $form;
    }
}