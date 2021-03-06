<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\FieldType\Slug\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\Generator\GeneratorInterface;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\FieldType\ValueObject\TemplateDir;
use Tardigrades\SectionField\ValueObject\Slug as SlugValueObject;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityPrePersistGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field, TemplateDir $templateDir): Template
    {
        $template = PrePersistTemplate::create(
            TemplateLoader::load(
                $templateDir . '/GeneratorTemplate/entity.prepersist.php.template'
            )
        );

        $asString = (string) $template;
        $asString = str_replace(
            '{{ propertyName }}',
            $field->getConfig()->getPropertyName(),
            $asString
        );

        $asString = str_replace(
            '{{ assignment }}',
            self::makeSlugAssignment(
                SlugValueObject::create(
                    $field->getConfig()->getGeneratorConfig()->toArray()['entity']['slugFields']
                )
            ),
            $asString
        );

        return Template::create($asString);
    }

    private static function makeSlugAssignment(SlugValueObject $slug): string
    {
        $assignment = [];
        foreach ($slug->toArray() as $element) {
            $element = explode('|', $element);
            $attach = '';
            if (count($element) > 1) {
                switch ($element[1]) {
                    case 'DateTime':
                        $attach = '->format(\'' . $element[2] . '\')';
                        break;
                }
            }
            $assignment[] = '$this->get' . Inflector::classify($element[0]) . '()' . $attach;
        }
        return 'Tardigrades\Helper\StringConverter::toSlug(' . implode(' . \'-\' . ', $assignment) . ');';
    }
}
