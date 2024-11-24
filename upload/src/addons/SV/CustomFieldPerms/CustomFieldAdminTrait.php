<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use SV\StandardLib\Helper;
use XF\Entity\AbstractField;
use XF\Mvc\FormAction;

trait CustomFieldAdminTrait
{
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
        $entityClassName = \XF::stringToClass($this->getClassIdentifier(), '%s\Entity\%s');
        $entity = Globals::$entities[$entityClassName] ?? null;
        if ($entity !== null)
        {
            foreach ($entity as $column => $details)
            {
                $fieldType = $details['filter_type'] ?? null;
                if ($fieldType !== null)
                {
                    $elements[$column] = $fieldType;
                }
            }

            $input = count($elements) !== 0 ? $this->filter($elements) : [];

            foreach ($entity as $column => $details)
            {
                if (!($details['isGroupList'] ?? false))
                {
                    continue;
                }

                $groupList = $this->filter('userGroup_'.$column, 'str');
                if ($groupList === 'all')
                {
                    $input[$column] = [-1];
                }
                else
                {
                    $input[$column] = $this->filter('userGroup_'.$column.'_ids', 'array-uint');
                }
            }

            $form->basicEntitySave($field, $input);
        }

        return $form;
    }
}