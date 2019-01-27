<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed only to registered users of the Cappasity platform.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Cappasity Inc <info@cappasity.com>
 * @copyright 2019 Cappasity Inc.
 */

namespace CappasitySDK\Client\Validator;

interface TypeInterface
{
    /**
     * @return \Respect\Validation\Validator
     */
    public static function configureValidator();

    /**
     * TODO Check in ValidatorWrapper that all required rule namespaces are appended to factory
     * @return array
     */
    public static function getRequiredRuleNamespaces();
}
