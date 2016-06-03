<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\View;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Renderer
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Renderer implements RendererInterface
{
    CONST TEMPLATE_DIR = 'VardiusListBundle:List:';

    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templateEngine = '.html.twig';

    /**
     * @param TwigEngine $templating
     */
    function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView(string $view, array $params = []):string
    {
        $template = null;
        if ($this->templating->exists($this->getTemplateName())) {
            $template = $this->getTemplateName();
        }

        $viewPath = $view;
        if ($template === null && $viewPath) {
            $templateDir = $viewPath . $this->getTemplateName() . $this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            $templateDir = self::TEMPLATE_DIR . $this->getTemplateName() . $this->templateEngine;
            if ($this->templating->exists($templateDir)) {
                $template = $templateDir;
            }
        }

        if ($template === null) {
            throw new ResourceNotFoundException('Vardius\Bundle\ListBundle\View\Renderer: Wrong template path');
        }

        return $this->templating->render($template, $params);
    }

    /**
     * @return string
     */
    protected function getTemplateName():string
    {
        return 'list';
    }
}
