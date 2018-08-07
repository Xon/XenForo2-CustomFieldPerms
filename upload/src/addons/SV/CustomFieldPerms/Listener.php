<?php


namespace SV\CustomFieldPerms;


use XF\Template\Templater;

class Listener
{
    public static function customFieldsEdit(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'input');
    }

    public static function customFieldsView(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'output_ui' : 'output_pp';
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
    }

    public static function customFieldsViewValues(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'output_ui');
    }
}
