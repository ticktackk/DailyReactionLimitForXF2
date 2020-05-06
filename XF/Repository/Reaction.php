<?php

namespace TickTackk\DailyReactionLimit\XF\Repository;

use XF\Entity\User as UserEntity;
use XF\Mvc\Entity\Finder;

/**
 * Class Reaction
 * 
 * Extends \XF\Repository\Reaction
 *
 * @package TickTackk\DailyReactionLimit\XF\Repository
 */
class Reaction extends XFCP_Reaction
{
    /**
     * @param int|UserEntity $reactionUserId
     *
     * @return Finder
     */
    public function findReactionsByReactionUserIdToday($reactionUserId) : Finder
    {
        $finder = $this->findReactionsByReactionUserId($reactionUserId);

        return $finder->where('reaction_date', '>=', \XF::$time - (24 * 3600));
    }

    /**
     * @param int|UserEntity $reactionUserId
     * @param string $contentType
     *
     * @return Finder
     */
    public function findReactionsByReactionUserIdTodayForContentType($reactionUserId, string $contentType) : Finder
    {
        return $this->findReactionsByReactionUserIdToday($reactionUserId)
            ->where('content_type', $contentType);
    }
}