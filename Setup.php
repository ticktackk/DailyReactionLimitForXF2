<?php

namespace TickTackk\DailyReactionLimit;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\AddOn\Manager as AddOnManager;
use XF\Entity\AddOn as AddOnEntity;

/**
 * Class Setup
 *
 * @package TickTackk\DailyReactionLimit
 */
class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1() : void
    {
        $this->applyDefaultPermissions();
    }

    /**
     * @param int|null $previousVersion
     *
     * @return bool
     */
    protected function applyDefaultPermissions(int $previousVersion = null) : bool
    {
        $applied = false;

        if (!$previousVersion)
        {
            $this->applyGlobalPermissionInt('general', 'tckDailyReactionLimit', -1);
            $this->applyGlobalPermissionInt('conversation', 'tckDailyReactionLimit', -1);
            $this->applyGlobalPermissionInt('profilePost', 'tckDailyReactionLimit', -1);
            $this->applyGlobalPermissionInt('profilePost', 'tckDRL_profilePostComment', -1);

            $this->applyDefaultAddOnPermissions('XFMG');
            $this->applyDefaultAddOnPermissions('XFRM');

            $applied = true;
        }

        return $applied;
    }

    /**
     * @param string $addOnId
     *
     * @return bool
     */
    public function applyDefaultAddOnPermissions(string $addOnId) : bool
    {
        $addOns = $this->app()->addOnManager()->getInstalledAddOns();
        if (!\array_key_exists('XFMG', $addOns))
        {
            return false;
        }

        /** @var AddOnEntity $addOn */
        $addOn = $addOns[$addOnId]->getInstalledAddOn();
        $addOnVersion = $addOn->version_id;
        switch ($addOnId)
        {
            case 'XFMG':
                if ($addOnVersion >= 2010070)
                {
                    $this->applyGlobalPermissionInt('xfmg', 'tckDRL_album', -1);
                    $this->applyGlobalPermissionInt('xfmg', 'tckDRL_media', -1);
                    $this->applyGlobalPermissionInt('xfmg', 'tckDRL_comment', -1);

                    return true;
                }
                break;

            case 'XFRM':
                if ($addOnVersion >= 2010070)
                {
                    $this->applyGlobalPermissionInt('resource', 'tckDailyReactionLimit', -1);

                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * @return AddOnManager
     */
    protected function addOnManager() : AddOnManager
    {
        return $this->app()->addOnManager();
    }
}