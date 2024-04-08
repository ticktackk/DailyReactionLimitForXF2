<?php

namespace TickTackk\DailyReactionLimit\XF\Entity;

use XF\Entity\Post as PostEntity;
use XF\Entity\ProfilePost as ProfilePostEntity;
use XF\Mvc\Entity\Entity;
use XFMG\Entity\Album as AlbumEntity;
use XFMG\Entity\Comment as CommentEntity;
use XFMG\Entity\MediaItem as MediaItemEntity;
use XFRM\Entity\ResourceUpdate as ResourceUpdateEntity;

/**
 * Class User
 * 
 * Extends \XF\Entity\User
 *
 * @package TickTackk\DailyReactionLimit\XF\Entity
 */
class User extends XFCP_User
{
    /**
     * @return int
     */
    public function getDailyReactionLimit() : int
    {
        return $this->hasPermission('general', 'tckDailyReactionLimit');
    }

    /**
     * @param Entity $content
     *
     * @return int|null
     */
    public function getDailyReactionLimitForContentType(Entity $content) :? int
    {
        switch ($content->getEntityContentType())
        {
            case 'post':
                /** @var PostEntity $content */
                $thread = $content->Thread;
                if (!$thread)
                {
                    return 0;
                }

                return $this->hasNodePermission($thread->node_id, 'tckDailyReactionLimit');

            case 'conversation_message':
                return $this->hasPermission('conversation', 'tckDailyReactionLimit');

            case 'profile_post':
                /** @var ProfilePostEntity $content */
                return $this->hasPermission('profilePost', 'tckDailyReactionLimit');

            case 'profile_post_comment':
                return $this->hasPermission('profilePost', 'tckDRL_profilePostComment');

            case 'resource_update':
                /** @var ResourceUpdateEntity $content */
                $resource = $content->Resource;
                if (!$resource)
                {
                    return 0;
                }

                return $resource->hasPermission('tckDailyReactionLimit');
            case 'xfmg_album':
                /** @var AlbumEntity $content */
                return $content->hasPermission('tckDRL_album');

            case 'xfmg_media':
                /** @var MediaItemEntity $content */
                return $content->hasPermission('tckDRL_media');

            case 'xfmg_comment':
                /** @var CommentEntity $content */
                $commentContainer = $content->Content;
                if (!$commentContainer)
                {
                    return 0;
                }

                return $commentContainer->hasPermission('tckDRL_comment');

            default:
                return -1; // allow all
        }
    }
}