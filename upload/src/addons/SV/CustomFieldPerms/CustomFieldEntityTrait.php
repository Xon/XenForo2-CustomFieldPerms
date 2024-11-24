<?php
/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Structure;

trait CustomFieldEntityTrait
{
    public function hasCustomFieldPerm(string $column, bool $raw = false): bool
    {
        $structure = $this->structure();

        if ($raw)
        {
            return isset($structure->columns['cfp_' . $column]);
        }

        return isset($structure->columns['cfp_' . $column . '_enable']) && isset($structure->columns['cfp_' . $column . '_val']);
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

        $table = Globals::$tables[$structure->table] ?? null;
        if (is_array($table))
        {
            foreach ($table as $column => $details)
            {
                $structure->columns[$column] = $details['entity'];
            }
        }

        return $structure;
    }
}