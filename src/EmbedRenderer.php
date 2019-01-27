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

use Twig_Error;
use Twig_Environment;

class EmbedRenderer
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array $params
     * @return string
     * @throws EmbedRenderer\Exception\RenderException
     */
    public function render(array $params)
    {
        if (!array_key_exists('viewId', $params) || !is_string($params['viewId']) || $params['viewId'] === '') {
            throw new EmbedRenderer\Exception\InvalidParamsException(
                'Cappasity 3D View ID is required to render template'
            );
        }

        try {
            return $this->twig->render('embed.html.twig', $params);
        } catch (Twig_Error $e) {
            throw new EmbedRenderer\Exception\RenderException('Error occurred while rendering embed code template', 0, $e);
        }
    }
}
