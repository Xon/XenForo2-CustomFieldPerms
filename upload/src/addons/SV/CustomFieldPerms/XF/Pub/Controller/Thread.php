<?php
namespace SV\CustomFieldPerms\XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

class Thread extends XFCP_Thread
{

    /**
     * Remove custom fields from thread view when visitor does not have permission to see them.
     *
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\View
     */
    public function actionIndex(ParameterBag $params)
    {
        $reply = parent::actionIndex($params);

        if ($reply instanceof View)
        {
            /** @var \XF\Entity\Post[] $posts */
            $posts = $reply->getParam('posts');
            foreach ($posts as $post)
            {
                // get custom field view permissions from data registry
                $permsCache = \XF::registry()->get('sedo_perms_ui_users');

                // get visitors user groups
                $visitorUserGroups = array_merge(
                    [\XF::visitor()->user_group_id],
                    \XF::visitor()->secondary_group_ids
                );

                // remove custom field from view if user doesn't have permission to see it
                /** @var \XF\Entity\UserProfile $profile */
                $profile = $post->getRelation('User')->getRelation('Profile');
                $customFields = $profile->getValue('custom_fields');
                foreach ($customFields as $key => $value)
                {
                    if (isset($permsCache[$key]))
                    {
                        $permittedUsergroups = @unserialize($permsCache[$key]);
                        if (
                            empty(array_intersect($visitorUserGroups, $permittedUsergroups)) &&
                            !in_array('all', $permittedUsergroups)
                        )
                        {
                            unset($customFields[$key]);
                        }
                    }
                }
                $profile->set('custom_fields', $customFields);
            }
        }

        return $reply;
    }
}
