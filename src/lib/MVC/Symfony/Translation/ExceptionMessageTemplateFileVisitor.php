<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Translation;

use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;

class ExceptionMessageTemplateFileVisitor extends DefaultPhpFileExtractor
{
    protected $methodsToExtractFrom = ['setmessagetemplate' => -1];

    protected $defaultDomain = 'repository_exceptions';
}

class_alias(ExceptionMessageTemplateFileVisitor::class, 'eZ\Publish\Core\MVC\Symfony\Translation\ExceptionMessageTemplateFileVisitor');
