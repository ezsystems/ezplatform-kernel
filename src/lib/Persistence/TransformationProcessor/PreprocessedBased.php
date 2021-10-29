<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\TransformationProcessor;

use Ibexa\Core\Persistence\TransformationProcessor;

/**
 * Class for processing a set of transformations, loaded from .tr files, on a string.
 */
class PreprocessedBased extends TransformationProcessor
{
    /**
     * Constructor.
     *
     * @param \Ibexa\Core\Persistence\TransformationProcessor\PcreCompiler $compiler
     * @param string $installDir Base dir for rule loading
     * @param array $ruleFiles
     */
    public function __construct(PcreCompiler $compiler, array $ruleFiles = [])
    {
        parent::__construct($compiler, $ruleFiles);
    }

    /**
     * Loads rules.
     *
     * @return array
     */
    protected function getRules()
    {
        if ($this->compiledRules === null) {
            $rules = [];

            foreach ($this->ruleFiles as $file) {
                $rules += require $file;
            }

            $this->compiledRules = $this->compiler->compile($rules);
        }

        return $this->compiledRules;
    }
}

class_alias(PreprocessedBased::class, 'eZ\Publish\Core\Persistence\TransformationProcessor\PreprocessedBased');
