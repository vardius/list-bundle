<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types\Type;

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Column\Types\AbstractType;

/**
 * OptionType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class OptionType extends AbstractType
{
    /** @var TwigEngine */
    protected $templating;

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
    public function getData($entity = null, array $options = []):array
    {
        return [
            'option' => $this,
            'entity' => $entity,
        ];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath)
    {
        parent::configureOptions($resolver, $property, $templatePath);

        $class = get_class($this);
        $resolver->setDefault('label', $this->templating->render($templatePath . strtolower(str_replace('Type', '', substr($class, strrpos($class, '\\') + 1))) . '.html.twig'));
        $resolver->remove('url');
        $resolver->remove('sort');
    }
}
