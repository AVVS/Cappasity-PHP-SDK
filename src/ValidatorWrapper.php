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

namespace CappasitySDK;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Factory;

use CappasitySDK\Client\Model\Request\RequestParamsInterface;
use CappasitySDK\Client\Exception\ValidationException;

class ValidatorWrapper
{
    private static $rulePrefixesToAppend = [
        'CappasitySDK\\Client\\Validator\\Rules',
    ];

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $typeClassName
     * @return Validator
     */
    public function buildByType($typeClassName)
    {
        if (!method_exists($typeClassName, 'configureValidator')) {
            throw new \LogicException('Type class must have method configureValidator()');
        }

        Validator::setFactory($this->factory);

        $validator = $typeClassName::configureValidator($this->factory);

        Validator::setFactory(null);

        return $validator;
    }

    /**
     * @param Validator $typeValidator
     * @param RequestParamsInterface $params
     * @return bool
     *
     * @throws ValidationException
     */
    public function assert(RequestParamsInterface $params, Validator $typeValidator)
    {
        Validator::setFactory($this->factory);

        try {
            return $typeValidator->assert($params);
        } catch (NestedValidationException $e) {
            throw ValidationException::fromNestedValidationException($e);
        } finally {
            Validator::setFactory(null);
        }
    }

    /**
     * @param RequestParamsInterface $params
     * @param Validator $typeValidator
     * @return bool
     */
    public function validate(RequestParamsInterface $params, Validator $typeValidator)
    {
        Validator::setFactory($this->factory);

        try {
            return $typeValidator->validate($params);
        } finally {
            Validator::setFactory(null);
        }
    }

    /**
     * @return static
     */
    public static function setUpInstance()
    {
        $factory = new Factory();
        foreach (self::$rulePrefixesToAppend as $rulePrefix) {
            $factory->appendRulePrefix($rulePrefix);
        }

        return new static($factory);
    }
}
