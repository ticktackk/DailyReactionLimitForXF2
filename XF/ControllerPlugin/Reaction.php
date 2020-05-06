<?php

namespace TickTackk\DailyReactionLimit\XF\ControllerPlugin;

use TickTackk\DailyReactionLimit\XF\ControllerPlugin\Exception\InvalidReactionLimitTypeSelectedException;
use TickTackk\DailyReactionLimit\XF\Entity\User as ExtendedUserEntity;
use XF\Mvc\Entity\Entity;
use XF\Entity\Reaction as ReactionEntity;
use XF\Mvc\Reply\Exception as ExceptionReply;
use TickTackk\DailyReactionLimit\XF\Repository\Reaction as ExtendedReactionRepo;

/**
 * Class Reaction
 * 
 * Extends \XF\ControllerPlugin\Reaction
 *
 * @package TickTackk\DailyReactionLimit\XF\ControllerPlugin
 */
class Reaction extends XFCP_Reaction
{
    /**
     * @param Entity $content
     * @param null $existingReaction
     *
     * @return ReactionEntity
     *
     * @throws ExceptionReply
     */
    protected function validateReactionAction(Entity $content, &$existingReaction = null)
    {
        $reaction = parent::validateReactionAction($content, $existingReaction);

        if ($reaction instanceof ReactionEntity && !$existingReaction)
        {
            return $this->assertCanReact($content, $reaction);
        }

        return $reaction;
    }

    /**
     * @param Entity $content
     * @param ReactionEntity $reaction
     *
     * @return ReactionEntity
     *
     * @throws ExceptionReply
     */
    protected function assertCanReact(Entity $content, ReactionEntity $reaction)
    {
        /** @var ExtendedUserEntity $visitor */
        $visitor = \XF::visitor();

        /** @var ExtendedReactionRepo $reactionRepo */
        $reactionRepo = $this->getReactionRepo();
        $reactionLimitType = $this->options()->tckDailyReactionLimit_limitType;

        switch ($reactionLimitType)
        {
            case 'global':
                $dailyReactionLimit = $visitor->getDailyReactionLimit();
                $reactionContentFinder = $reactionRepo->findReactionsByReactionUserIdToday($visitor->user_id);
                break;

            case 'content':
                $dailyReactionLimit = $visitor->getDailyReactionLimitForContentType($content);
                $reactionContentFinder = $reactionRepo->findReactionsByReactionUserIdTodayForContentType(
                    $visitor->user_id, $content->getEntityContentType()
                );
                break;

            default:
                throw new InvalidReactionLimitTypeSelectedException($reactionLimitType);
        }

        if ($dailyReactionLimit === null || $dailyReactionLimit === -1) // content type not supported or no limit
        {
            return $reaction;
        }

        if ($dailyReactionLimit === 0) // not allowed ??? why not just disable reaction?????
        {
            throw $this->exception($this->noPermission()); // for now just show generic no permission
        }

        $reactionContentsInLast24Hours = $reactionContentFinder->total();
        if ($reactionContentsInLast24Hours >= $dailyReactionLimit) // >= in case the add-on was installed/enabled after user has reached the limit
        {
            throw $this->exception($this->noPermission(
                \XF::phrase('tckDailyReactionLimit_you_have_reached_the_amount_of_times_you_can_react_today')
            ));
        }

        return $reaction;
    }
}