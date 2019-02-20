# Cappasity SDK API

(c) Copyright 2019, Cappasity Inc. All rights reserved.

Cappasity SDK includes three components – `Client`, `EmbedRenderer` and `PreviewImageSrcGenerator`.

`Client` is responsible for making HTTP requests to Cappasity API. `EmbedRenderer` allows you to generate iFrame code to 
embed your HTML. In case you want to display Cappasity 3D View preview image `PreviewImageSrcGenerator` component helps you
to get a link to an image that fits desired sizes, format, quality, etc. 

## Table of Contents

* [Client](#client)
  * [Basic usage](#basic-usage)
  * [Send error reports](#send-error-reports)
    * [Send reports to Cappasity developers](#send-reports-to-cappasity-developers)
    * [Send reports to your own Sentry account](#send-reports-to-your-own-sentry-account)
  * [Other HTTP clients support](#other-http-clients-support)
  * [Create Client instance manually](#create-client-instance-manually)
  * [API](#api)
    * [Error handling](#error-handling)
      * [AuthorizationAssertionException](#authorizationassertionexception)
      * [ValidationException](#validationexception)
      * [RequestException](#requestexception)
    * [Register sync job](#register-sync-job)
      * [HTTP Push type](#http-push-type)
      * [HTTP Pull type](#http-pull-type)
      * [Errors](#errors)
    * [Get pull job list](#get-pull-job-list)
      * [Errors](#errors-1)
    * [Get pull job result](#get-pull-job-result)
      * [Errors](#errors-2)
    * [Acknowledge pull job list](#acknowledge-pull-job-list)
      * [Errors](#errors-3)
* [EmbedRenderer](#embedrenderer)
  * [Render embed code](#render-embed-code)
    * [Rendered code example](#rendered-code-example)
* [PreviewImageSrcGenerator](#previewimagesrcgenerator)
  * [Generate preview link](#generate-preview-link)
  * [Options list](#options-list)
    * [Modifiers list](#modifiers-list)
    * [Modifiers examples](#modifiers-examples)
    * [Overlays examples](#overlays-examples)   

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

## Send error reports
We use Raven to capture errors. Raven is a PHP client for Sentry error tracking system. By default, we send error 
reports to our private development account.

### Send reports to Cappasity developers 
Set `sendReports` option to `true`. This setting enables error reporting. Once enabled, captured errors are sent to the 
private Cappasity error tracking system. It allows us to help you to troubleshoot development issues.
```php 
use CappasitySDK\ClientFactory;

$client = ClientFactory::getClientInstance([
    'apiToken' => 'your.api.token',
    'sendReports' => true,
]);
```

### Send reports to your own Sentry account
Our `ClientFactory` allows you to override `Raven_Client` constructor parameters. Explore `\CappasitySDK\ClientFactory`
class code for more details. 
```php 
use CappasitySDK\ClientFactory;

$client = ClientFactory::getClientInstance([
    'apiToken' => 'your.api.token',
    'sendReports' => true,
    'reportableClient' => [
        'ravenClient' => [
            'optionsOrDsn' => 'https://3736a7965d59423c867105ee4ba47de2@sentry.io/137605', // Paste your DSN secret
        ]
    ]
]);
```

## Other HTTP clients support
By default, we use Guzzle 5 HTTP client. If you want to use another client instead, you can implement 
`\CappasitySDK\TransportInterface` and [create `\CappasitySDK\Client` instance manually](#create-client-instance-manually).

## Create Client instance manually
Explore `\CappasitySDK\ClientFactory::getClientInstance()` method and implement your own instantiation.

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

# PreviewImageSrcGenerator

## Generate preview link
* Set up `PreviewImageSrcGenerator`
* Provide Cappasity Account username and Cappasity 3D View ID 
* Generate links:
```php
use CappasitySDK\PreviewImageSrcGeneratorFactory as GeneratorFactory;
use CappasitySDK\PreviewImageSrcGenerator as Generator; 

$generator = new GeneratorFactory::getGeneratorInstance();

$originalPreview = $generator->generatePreviewImageSrc('username', '38020fdf-5e11-411c-9116-1610339d59cf');

// Explore and use \CappasitySDK\PreviewImageSrcGenerator class constants and static arrays with available options for 
// better code style:
$modifiedPreview = $generator->generatePreviewImageSrc('username', '38020fdf-5e11-411c-9116-1610339d59cf', [
    'format' => Generator::FORMAT_PNG, // 'png'
    'overlay' => Generator::OVERLAY_3DP_2X, // '3dp@2x'
    'modifiers' => [
        'square' => 250,
        'quality' => 30, 
    ],
]);  
``` 

## Options list
| Parameter   | Description |
|-------------|-------------|
| `format`    | `jpeg`, `jpg`, `png`, `gif`, `webp` |
| `overlay`   | `3dp`, `3dp@2x`, `3dp@3x`[(see examples)](#overlays-examples) |
| `modifiers` | See the [list of available modifiers](#modifiers-list) and [examples](#modifiers-examples)  |

### Modifiers list

| Modifier     | Description                                                                                                               | Example                                    |
|--------------|---------------------------------------------------------------------------------------------------------------------------|--------------------------------------------|
| `crop`       | Crop type                                                                                                                 | `fit`, `fill`, `cut`, `pad`                |
| `height`     | Height, px                                                                                                                | 300                                        |
| `width`      | Width, px                                                                                                                 | 200                                        |
| `square`     | Width and height, px                                                                                                      | 250                                        |
| `top`        | Crop start from top, px                                                                                                   | 10                                         |
| `left`       | Crop start from left, px                                                                                                  | 10                                         |
| `gravity`    | Image placement within resulting image (center, north, south, west, east, north-east, north-west, south-east, south-west) | `c`, `n`, `s`, `w`, `e`, `ne`, `nw`, `se`, `sw` |
| `quality`    | Quality factor, bpp                                                                                                       | 100                                        |
| `background` | Background color (hash-prefixed 6-char hex value)                                                                         | `#ffffff`                                  |

### Modifiers examples

Full-size original previews links:
[THE RING (1012 x 1024 px)](https://api.cappasity.com/api/files/preview/cappasity/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | [THE WATCH (1024 x 526 px)](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfit/14b8f4f4-6e89-4a6f-8535-651c09d180b3)

By default `fit` crop is applied:

| square=300 | width=200, height=400, crop=`fit` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfit/34ddd305-bb53-4fd7-8dce-8555fc5a269f)
![](https://api.cappasity.com/api/files/preview/cappasity/s300/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfit/14b8f4f4-6e89-4a6f-8535-651c09d180b3)

Cut crop:

| square=300 | width=200, height=400, crop=`cut` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-ccut/34ddd305-bb53-4fd7-8dce-8555fc5a269f)
![](https://api.cappasity.com/api/files/preview/cappasity/s300/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-ccut/14b8f4f4-6e89-4a6f-8535-651c09d180b3)

Pad crop:

| square=300 | width=200, height=400, crop=`pad` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cpad/34ddd305-bb53-4fd7-8dce-8555fc5a269f)
![](https://api.cappasity.com/api/files/preview/cappasity/s300/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cpad/14b8f4f4-6e89-4a6f-8535-651c09d180b3)


Fill crop:

| square=300 | width=200, height=400, crop=`fill` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill/34ddd305-bb53-4fd7-8dce-8555fc5a269f)
![](https://api.cappasity.com/api/files/preview/cappasity/s300/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill/14b8f4f4-6e89-4a6f-8535-651c09d180b3)

You can change the starting coordinates of the crop using `left` and `top` modifiers:

| width=200, height=400, crop=`fill` | width=200, height=400, crop=`fill`, left=120 |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill-x120/34ddd305-bb53-4fd7-8dce-8555fc5a269f)

| width=200, height=400, crop=`fill` | width=200, height=400, crop=`fill`, left=273 |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill-x273/14b8f4f4-6e89-4a6f-8535-651c09d180b3)

Also you can make it with specifying the `gravity`:

| square=300 | width=200, height=400, crop=`fill`, gravity=`e` (east) |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/w200-h400-cfill-ge/34ddd305-bb53-4fd7-8dce-8555fc5a269f)

Choose reasonable preview `quality`:

| square=300 (resulted in ~4.6Kb) | square=300, quality=30 (resulted in ~1.5Kb) | square=300, quality=50 (resulted in ~2Kb) |
:-------------------------:|:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s300/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/s200-q30/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/s200-q50/34ddd305-bb53-4fd7-8dce-8555fc5a269f)

On the 3D Cappasity View upload a person chooses a respective background color. We store it in 3D View metadata.
You can use that color as preview background color, otherwise it would be white:

| width=200, height=300, crop=cpad | width=200, height=300, background=000000 |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/h300-w200-cpad/14b8f4f4-6e89-4a6f-8535-651c09d180b3) | ![](https://api.cappasity.com/api/files/preview/cappasity/h300-w200-cpad-b000000/14b8f4f4-6e89-4a6f-8535-651c09d180b3)


### Overlays examples
An overlay is an image that covers the original preview image.
Choose reasonable overlay quality:

##### 3dp
| square=200 | square=200, overlay=`3dp` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f?o=3dp) 

##### 3dp@2x
| square=200 | square=200, overlay=`3dp@2x` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f?o=3dp@2x)

##### 3dp@3x
| square=200 | square=200, overlay=`3dp@3x` |
:-------------------------:|:-------------------------:
![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f) | ![](https://api.cappasity.com/api/files/preview/cappasity/s200/34ddd305-bb53-4fd7-8dce-8555fc5a269f?o=3dp@3x)
