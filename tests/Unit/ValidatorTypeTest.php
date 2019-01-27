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

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

use CappasitySDK\Client\Model\Request\Process\JobsRegisterSyncPost as JobsRegisterSyncPostModel;
use CappasitySDK\Client\Model\Request\Process\JobsPullListGet as JobsPullListGetModel;
use CappasitySDK\Client\Model\Request\Process\JobsPullResultGet as JobsPullResultGetModel;
use CappasitySDK\Client\Model\Request\Process\JobsPullAckPost as JobsPullAckPostModel;
use CappasitySDK\Client\Validator\Type\Request\Process\JobsRegisterSyncPost as JobsRegisterSyncPostType;
use CappasitySDK\Client\Validator\Type\Request\Process\JobsPullListGet as JobsPullListGetType;
use CappasitySDK\Client\Validator\Type\Request\Process\JobsPullResultGet as JobsPullResultGetType;
use CappasitySDK\Client\Validator\Type\Request\Process\JobsPullAckPost as JobsPullAckPostType;

class ValidatorTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideJobsRegisterSyncPostData
     * @param array $fromDataArgs
     * @param bool $shouldBeValid
     * @param null|string $expectedError
     */
    public function testValidateRegisterSyncJobPost(array $fromDataArgs, $shouldBeValid, $expectedError = null)
    {
        $params = JobsRegisterSyncPostModel::fromData(...$fromDataArgs);

        foreach (JobsRegisterSyncPostType::getRequiredRuleNamespaces() as $namespace) {
            v::with($namespace);
        }

        $validator = JobsRegisterSyncPostType::configureValidator();

        $this->assertValidator($validator, $params, $shouldBeValid, $expectedError);
    }

    public function provideJobsRegisterSyncPostData()
    {
        return [
            [
                [
                    [
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'push:http',
                    'http://somewhere.com/over/the/rainbow',
                ],
                true
            ],
            [
                [
                    [
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'pull'
                ],
                true
            ],
            [
                [
                    [
                        [
                            'id' => 123,
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'push:http',
                    'http://somewhere.com/over/the/rainbow',
                ],
                false,
                '- id must be a string',
            ],
            [
                [
                    [
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['invalid-sku!!!'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'push:http',
                    'http://somewhere.com/over/the/rainbow',
                ],
                false,
                '- "invalid-sku!!!" must match pattern /^[0-9A-Za-z_\-.]{1,50}$/'
            ],
            [
                [
                    [
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'push:http',
                ],
                false,
                // TODO don't like this error message because it does not tell when it must me a URL
                '- callbackUrl must be a URL'
            ],
            [
                [
                    [
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ],
                    ],
                    'pull',
                    'http://somewhere.com/over/the/rainbow',
                ],
                false,
                // TODO don't like this error message because it does not tell when it must be null
                '- callbackUrl must be null'
            ],
            [
                [
                    array_fill(
                        0,
                        501,
                        [
                            'id' => 'inner-product-id',
                            'aliases' => ['Bear'],
                            'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
                        ]
                    ),
                    'push:http',
                    'http://somewhere.com/over/the/rainbow',
                ],
                false,
                '- items must have a length between 1 and 500'
            ]
        ];
    }

    /**
     * @dataProvider provideGetPullHobListData
     * @param array $fromDataArgs
     * @param bool $shouldBeValid
     * @param null|string $expectedError
     */
    public function testValidateGetPullJobList(array $fromDataArgs, $shouldBeValid, $expectedError = null)
    {
        $params = JobsPullListGetModel::fromData(...$fromDataArgs);

        foreach (JobsPullListGetType::getRequiredRuleNamespaces() as $namespace) {
            v::with($namespace);
        }

        $validator = JobsPullListGetType::configureValidator();

        $this->assertValidator($validator, $params, $shouldBeValid, $expectedError);
    }

    public function provideGetPullHobListData()
    {
        return [
            [
                [null, null],
                true,
            ],
            [
                [40, 100],
                true,
            ],
            [
                [50, null],
                false,
                join(PHP_EOL, [
                    '- At least one of these rules must pass for limit',
                    '  - limit must be null',
                    '  - limit must be less than or equal to 40'
                ]),
            ],
            [
                [-100, 100],
                false,
                join(PHP_EOL, [
                    '- At least one of these rules must pass for limit',
                    '  - limit must be null',
                    '  - limit must be greater than or equal to 1'
                ]),
            ],
            [
                [-100, -100],
                false,
                join(PHP_EOL, [
                    '- These rules must pass for JobsPullListGet',
                    '  - At least one of these rules must pass for limit',
                    '    - limit must be null',
                    '    - limit must be greater than or equal to 1',
                    '  - At least one of these rules must pass for cursor',
                    '    - cursor must be null',
                    '    - cursor must be greater than or equal to 0',
                ]),
            ]
        ];
    }

    /**
     * @dataProvider provideJobsPullResultGetData
     * @param array $fromDataArgs
     * @param bool $shouldBeValid
     * @param null $expectedError
     */
    public function testValidateJobsPullResultGet(array $fromDataArgs, $shouldBeValid, $expectedError = null)
    {
        $params = JobsPullResultGetModel::fromData(...$fromDataArgs);

        foreach (JobsPullResultGetType::getRequiredRuleNamespaces() as $namespace) {
            v::with($namespace);
        }

        $validator = JobsPullResultGetType::configureValidator();

        $this->assertValidator($validator, $params, $shouldBeValid, $expectedError);
    }

    public function provideJobsPullResultGetData()
    {
        return [
            [
                [
                    'a9673347-8f2e-4caa-83e9-4139d7473c2f:A1',
                ],
                true,
            ],
            [
                [
                    null,
                ],
                false,
                '- jobId must be a string'
            ],
            [
                [
                    123,
                ],
                false,
                '- jobId must be a string'
            ]
        ];
    }

    /**
     * @dataProvider providePullAckPostData
     * @param [] $fromDataArgs
     * @param bool $shouldBeValid
     * @param null|string $expectedError
     */
    public function testValidateJobsPullAckPost(array $fromDataArgs, $shouldBeValid, $expectedError = null)
    {
        $params = JobsPullAckPostModel::fromData(...$fromDataArgs);

        foreach (JobsPullAckPostType::getRequiredRuleNamespaces() as $namespace) {
            v::with($namespace);
        }

        $validator = JobsPullAckPostType::configureValidator();

        $this->assertValidator($validator, $params, $shouldBeValid, $expectedError);
    }

    // todo rename
    private function assertValidator(\Respect\Validation\Validator $validator, $params, $shouldBeValid, $expectedError)
    {
        try {
            $validator->assert($params);

            if ($shouldBeValid === false) {
                $this->fail('Value should be considered invalid');
            }
        } catch (NestedValidationException $e) {
            $actualError = $e->getFullMessage();

            if ($shouldBeValid) {
                $this->fail("Value should be considered valid, but assertion failed: {$actualError}");
            }

            $this->assertEquals($expectedError, $actualError);
        }
    }

    public function providePullAckPostData()
    {
        return [
            [
                [
                    ['a9673347-8f2e-4caa-83e9-4139d7473c2f:A1'],
                ],
                true,
            ],
            [
                [
                    ['a9673347-8f2e-4caa-83e9-4139d7473c2f:A1', 'a9673347-8f2e-4caa-83e9-4139d7473c2f:B2'],
                ],
                true,
            ],
            [
                [
                    []
                ],
                true,
            ],
            [
                [
                    [123],
                ],
                false,
                '- 123 must be a string'
            ],
        ];
    }
}
