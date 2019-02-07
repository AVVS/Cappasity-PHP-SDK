# Cappasity SDK API

(c) Copyright 2019, Cappasity Inc. All rights reserved.

Cappasity SDK includes two components – `Client` and `EmbedRenderer`.

`Client` is responsible for making HTTP requests to Cappasity API. `EmbedRenderer` allows you to generate iFrame code to 
embed your HTML.

## Table of Contents

- [Client](#client)
  * [Basic usage](#basic-usage)
  * [Send error reports to Cappasity developers](#send-error-reports-to-cappasity-developers)
  * [Other HTTP clients support](#other-http-clients-support)
  * [API](#api)
    + [Error handling](#error-handling)
      - [AuthorizationAssertionException](#authorizationassertionexception)
      - [ValidationException](#validationexception)
      - [RequestException](#requestexception)
    + [Register sync job](#register-sync-job)
      - [HTTP Push type](#http-push-type)
      - [HTTP Pull type](#http-pull-type)
      - [Errors](#errors)
    + [Get pull job list](#get-pull-job-list)
      - [Errors](#errors-1)
    + [Get pull job result](#get-pull-job-result)
      - [Errors](#errors-2)
    + [Acknowledge pull job list](#acknowledge-pull-job-list)
      - [Errors](#errors-3)
- [EmbedRenderer](#embedrenderer)
  * [Render embed code](#render-embed-code)
    + [Rendered code example](#rendered-code-example)

# Client

## Basic usage
```php
use CappasitySDK\ClientFactory;
use CappasitySDK\Client\Model\Request;
use CappasitySDK\Client\Model\Response;

// instantiate client with `ClientFactory`
$client = ClientFactory::getClientInstance([
  'apiToken' => 'your.api.token'
]);

// create parameter object
$params = Request\Process\JobsRegisterSyncPost::fromData(
  [
    [
      'id' => 'inner-product-id',
      'aliases' => ['SkuGoesHere'],
      'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
    ]
  ],
  'push:http',
  'http://your-callback-url.com/foo/bar'
);

$response = $client->registerSyncJob($params);

/** @var Response\Process\JobsRegisterSyncPost $data */
$data = $response->getBodyData();
```

Client methods return wrapped and transformed Guzzle response.
If you need more information about the response you can always refer to the original response.
```php
$originalResponse = $response->getOriginalResponse();
```

## Send error reports to Cappasity developers
Just set `sendReports` option to `true`:
```php 
use CappasitySDK\ClientFactory;

$client = ClientFactory::getClientInstance([
    'apiToken' => 'your.api.token'
    'sendReports' => true
]);
```

## Other HTTP clients support
By default, we use Guzzle 5 HTTP client. If you want to use another client instead, you can implement 
`\CappasitySDK\TransportInterface` and create client instance manually.

## API

### Error handling
All `Client` exceptions are inherited from `CappasitySDK\Client\Exception\ClientException`.
The ones below you can prevent and handle.

#### AuthorizationAssertionException
Each time you make a request we validate the presence of API token you have instantiated your `Client` with. 
`CappasitySDK\Client\Exception\AuthorizationAssertionException` is thrown in case the token is empty or not a string.
Avoid it by passing valid API token.

#### ValidationException
Request parameters primary validation is implemented on the SDK side. When validation fails,  
`CappasitySDK\Client\Exception\ValidationException` is thrown. Avoid it by providing valid params.

#### RequestException
`CappasitySDK\Client\Exception\RequestException` is generally thrown when an HTTP error occurs.
Possible error codes and their descriptions are listed next to each SDK method example.
RequestException instance also holds original request and response objects in case you need more info.

```php
use CappasitySDK\Client\Exception\RequestException;

// ... init $params   

try {
  $response = $client->registerSyncJob($params); 
} catch (RequestException $e) {
  $httpErrorCode = $e->getCode();
  $parsedErrorMessage = $e->getMessage();
  $originalRequest = $e->getRequest();
  $originalResponse = $e->getResponse(); // here you can get more details about errors if response was received
}
```

### Register sync job
#### HTTP Push type
```php
use CappasitySDK\Client\Model\Request;
 
$registerSyncJobId = $client
  ->registerSyncJob(Request\Process\JobsRegisterSyncPost::fromData(
    [
      [
        'id' => 'inner-product-id',
        'aliases' => ['SkuGoesHere'],
        'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
      ]
    ],
    'push:http',
    'http://your-callback-url.com/foo/bar
  ))
  ->getBodyData()
  ->getId();
```

#### HTTP Pull type
```php
use CappasitySDK\Client\Model\Request;

$registerSyncJobId = $client
  ->registerSyncJob(Request\Process\JobsRegisterSyncPost::fromData(
    [
      [
        'id' => 'inner-product-id',
        'aliases' => ['SkuGoesHere'],
        'capp' => '38020fdf-5e11-411c-9116-1610339d59cf',
      ]
    ],
    'pull'
  ))
  ->getBodyData()
  ->getId();
```

#### Errors
| Code | Description                   |
|:----:|-------------------------------|
| 401  | Authorization error           |
| 413  | Sync units limit reached      |
| 429  | Multiple concurrent requests  |

### Get pull job list
```php
use CappasitySDK\Client\Model\Request;
 
$jobList = $client
  ->getPullJobList(Request\Process\JobsPullListGet::fromData($limit, $cursor))
  ->getBodyData();
```

#### Errors
| Code | Description                   |
|:----:|-------------------------------|
| 401  | Authorization error           |

### Get pull job result
```php
use CappasitySDK\Client\Model\Request;
 
$response = $client->getPullJobResult(Request\Process\JobsPullResultGet::fromData($jobId));

foreach ($response->getBodyData()->getData() as $jobItemResult) {
    // handle results
}
```
#### Errors
| Code | Description                   |
|:----:|-------------------------------|
| 401  | Authorization error           |
| 404  | Job data missing              |

### Acknowledge pull job list
```php
use CappasitySDK\Client\Model\Request;

$ackJobCount = $client
  ->ackPullJobList(Request\Process\JobsPullAckPost::fromData($jobIds))
  ->getBodyData()
  ->getData()
  ->getAck();
```

When no jobs to acknowledge were found, the result is 0.
#### Errors
| Code | Description                   |
|:----:|-------------------------------|
| 401  | Authorization error           |

# EmbedRenderer

## Render embed code
The only required parameter is `viewId`, which holds Cappasity 3D View ID. If it is not present or empty,  
`CappasitySDK\EmbedRenderer\Exception\InvalidParamsException` is thrown.

```php
use CappasitySDK\EmbedRendererFactory;

$renderer = EmbedRendererFactory::getRenderer();
$embedCode = $renderer->render(['viewId' => '38020fdf-5e11-411c-9116-1610339d59cf']);
```

Full params list:

| Parameter           | Description                                                                               |
|---------------------|-------------------------------------------------------------------------------------------|
| `viewId`            | Cappasity 3D View ID                                                                      |
| `width`             | iFrame width                                                                              |
| `height`            | iFrame height                                                                             |
| `autoRun`           | Whether to start the player (widget) automatically or display the preview and play button |
| `closeButton`       | Show close button                                                                         |
| `logo`              | Show Cappasity logo                                                                       |
| `analytics`         | Enable analytics                                                                          |
| `autoRotate`        | Start automatic rotation                                                                  |
| `autoRotateTime`    | Rotation time of the full turn, seconds                                                   |
| `autoRotateDelay`   | Delay if rotation was interrupted, seconds                                                |
| `autoRotateDir`     | Autorotate direction (clockwise is `1`, counter-clockwise is `-1`)                        |
| `hideFullScreen`    | Hide fullscreen view button                                                               |
| `hideAutoRotateOpt` | Hide autorotate button                                                                    |
| `hideSettingsBtn`   | Hide settings button                                                                      |
| `enableImageZoom`   | Enable zoom                                                                               |
| `zoomQuality`       | Zoom quality (SD is `1`, HD is `2`)                                                       |
| `hideZoomOpt`       | Hide zoom button                                                                          |

```php
$embedCode = $renderer->render([
  'viewId' => '38020fdf-5e11-411c-9116-1610339d59cf',
  'width' => '100%',
  'height' => '600',
  'autoRun' => true,
  'closeButton' => false,
  'logo' => true,
  'analytics' => true,
  'autoRotate' => false,
  'autoRotateTime' => 10,
  'autoRotateDelay' => 2,
  'autoRotateDir' => 1,
  'hideFullScreen' => true,
  'hideAutoRotateOpt' => true,
  'hideSettingsBtn' => false,
  'enableImageZoom' => true,
  'zoomQuality' => 2,
  'hideZoomOpt' => false,
]);
```

### Rendered code example
```html
<iframe
    allowfullscreen
    mozallowfullscreen="true"
    webkitallowfullscreen="true"
    width="100%"
    height="600"
    frameborder="0"
    style="border:0;"
    onmousewheel=""
    src="https://api.cappasity.com/api/player/38020fdf-5e11-411c-9116-1610339d59cf/embedded?autorun=1&closebutton=0&logo=1&autorotate=0&autorotatetime=10&autorotatedelay=2&autorotatedir=1&hidefullscreen=1&hideautorotateopt=1&hidesettingsbtn=0&enableimagezoom=1&zoomquality=2&hidezoomopt=0&analytics=1"
></iframe>
```
