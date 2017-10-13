<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityUseGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field): Template
    {
        return Template::create(
            (string) TemplateLoader::load(
                $field->getFieldType()->getInstance()->directory() .
                '/GeneratorTemplate/entity.use.php.template'
            )
        );
    }
}
