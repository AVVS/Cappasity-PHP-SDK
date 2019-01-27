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

namespace CappasitySDK\Tests\Unit;

class ValidatorWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Respect\Validation\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factoryMock;

    public function setUp()
    {
        $this->factoryMock = $this->getMockBuilder(\Respect\Validation\Factory::class)
            ->disableOriginalConstructor()
            ->setMethods(['appendRulePrefix'])
            ->getMock();
    }

    /**
     *
     */
    public function testAssert()
    {
        $validatorWrapper = new \CappasitySDK\ValidatorWrapper($this->factoryMock);

        /** @var \CappasitySDK\Client\Model\Request\Process\JobsPullAckPost|\PHPUnit_Framework_MockObject_MockObject $paramsMock */
        $paramsMock = $this->getMockBuilder(\CappasitySDK\Client\Model\Request\Process\JobsPullAckPost::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Respect\Validation\Validator|\PHPUnit_Framework_MockObject_MockObject $typeValidatorMock */
        $typeValidatorMock = $this->getMockBuilder(\Respect\Validation\Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFactory', 'assert'])
            ->getMock();

        $expectedResult = true;

        $typeValidatorMock
            ->expects($this->once())
            ->method('assert')
            ->with($paramsMock)
            ->willReturn($expectedResult);

        $actualResult = $validatorWrapper->assert($paramsMock, $typeValidatorMock);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAssertAndThrowWrappedException()
    {
        $validatorWrapper = new \CappasitySDK\ValidatorWrapper($this->factoryMock);

        /** @var \CappasitySDK\Client\Model\Request\Process\JobsPullAckPost|\PHPUnit_Framework_MockObject_MockObject $paramsMock */
        $paramsMock = $this->getMockBuilder(\CappasitySDK\Client\Model\Request\Process\JobsPullAckPost::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Respect\Validation\Validator|\PHPUnit_Framework_MockObject_MockObject $typeValidatorMock */
        $typeValidatorMock = $this->getMockBuilder(\Respect\Validation\Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFactory', 'assert'])
            ->getMock();

        /** @var \Respect\Validation\Exceptions\NestedValidationException|\PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder(\Respect\Validation\Exceptions\NestedValidationException::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFullMessage'])
            ->getMock();

        $typeValidatorMock
            ->expects($this->once())
            ->method('assert')
            ->with($paramsMock)
            ->willThrowException($exceptionMock);

        $expectedMessage = 'error message';
        $exceptionMock
            ->expects($this->once())
            ->method('getFullMessage')
            ->willReturn($expectedMessage);

        try {
            $validatorWrapper->assert($paramsMock, $typeValidatorMock);

            $this->fail('Expected an exception to be thrown');
        } catch (\CappasitySDK\Client\Exception\ValidationException $e) {
            $this->assertEquals($expectedMessage, $e->getMessage());
        } catch (\Exception $e) {
            $exceptionType = get_class($e);
            $this->fail("Unexpected exception type {$exceptionType}");
        }
    }
}
