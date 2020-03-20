<?php

namespace TickTackk\DailyReactionLimit;

use XF\AddOn\AddOn as AddOn;
use XF\Entity\AddOn as AddOnEntity;

/**
 * Class Listener
 *
 * @package TickTackk\DailyReactionLimit
 */
class Listener
{
    /**
     * @var array
     */
    protected static $supportedAddOns = [
        'XFMG' => 2010070,
        'XFRM' => 2010070
    ];

    /**
     * @return array
     */
    protected static function getSupportedAddOns() : array
    {
        return static::$supportedAddOns;
    }

    /**
     * Called when the post-install code for an add-on has been run.
     *
     * Event hint: The add-on ID for the add-on being installed.
     * 
     * @param AddOn       $addOn          The AddOn object for the add-on being installed.
     * @param AddOnEntity $installedAddOn The newly created add-on entity.
     * @param array       $json           An array decoded from the add-on's addon.json
     *                                         file.
     * @param array       $stateChanges   An array for storing state changes such as
     *                                         post-install controller redirects.
     */
    public static function addonPostInstall(AddOn $addOn, AddOnEntity $installedAddOn, array $json, array $stateChanges) : void
    {
        $addOnId = $addOn->getAddOnId();
        $supportedAddOns = static::getSupportedAddOns();
        if (!\array_key_exists($addOnId, $supportedAddOns))
        {
            return;
        }

        $minimumAddOnVersion = $supportedAddOns[$addOnId];
        if ($installedAddOn->version_id < $minimumAddOnVersion)
        {
            return;
        }

        $setup = new Setup($addOn, \XF::app());
        $setup->applyDefaultAddOnPermissions($addOnId);
    }
}