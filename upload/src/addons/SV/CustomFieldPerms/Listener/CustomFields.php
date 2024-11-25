<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\CustomFieldPerms\Listener;

use SV\CustomFieldPerms\Repository\Field as FieldRepo;
use XF\Template\Templater;

abstract class CustomFields
{
    public static function customFieldsEdit(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars): void
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'input');
        }
    }

    public static function customFieldsView(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars): void
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'output_ui' : 'output_pp';
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
        }
    }

    public static function customFieldsViewValues(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'output_ui');
        }
    }
}
