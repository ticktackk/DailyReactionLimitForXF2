<?php

namespace TickTackk\DailyReactionLimit\XF\ControllerPlugin\Exception;

use Throwable;

/**
 * Class InvalidReactionLimitTypeSelectedException
 *
 * @package TickTackk\DailyReactionLimit\XF\ControllerPlugin\Exception
 */
class InvalidReactionLimitTypeSelectedException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    protected $reactionLimitType;

    /**
     * InvalidReactionLimitTypeSelectedException constructor.
     *
     * @param string $reactionLimitType
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $reactionLimitType, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid reaction limit type selected.', $code, $previous);

        $this->reactionLimitType = $reactionLimitType;
    }

    /**
     * @return string
     */
    public function getReactionLimitType() : string
    {
        return $this->reactionLimitType;
    }
}