<?php

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Structure;

trait CustomFieldEntityTrait
{
    public function hasCustomFieldPerm($column)
    {
        /** @var Structure $structure */
        $structure = $this->structure();

        return isset($structure->columns['cfp_' . $column . '_enable']);
    }

    /**
     * Insert additional columns into ThreadField
     *
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        foreach (Setup::$tables1[self::$tableName] as $column => $details)
        {
            $structure->columns[$column] = ['type' => $details['entity_type'], 'default' => $details['entity_default']];
        }

        return $structure;
    }
}