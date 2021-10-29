<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\TransformationProcessor;

use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\Core\Persistence\TransformationProcessor\DefinitionBased\Parser;

/**
 * Class for processing a set of transformations, loaded from .tr files, on a string.
 */
class DefinitionBased extends TransformationProcessor
{
    /**
     * Transformation parser.
     *
     * @var \Ibexa\Core\Persistence\TransformationProcessor\DefinitionBased\Parser
     */
    protected $parser = null;

    /**
     * Construct instance of TransformationProcessor\DefinitionBased.
     *
     * Through the $ruleFiles array, a list of files with full text
     * transformation rules is given. These files are parsed by
     * {@link \Ibexa\Core\Persistence\TransformationProcessor\DefinitionBased\Parser}
     * and then used for normalization in the full text search.
     *
     * @param \Ibexa\Core\Persistence\TransformationProcessor\DefinitionBased\Parser $parser
     * @param \Ibexa\Core\Persistence\TransformationProcessor\PcreCompiler $compiler
     * @param array $ruleFiles
     */
    public function __construct(Parser $parser, PcreCompiler $compiler, array $ruleFiles = [])
    {
        parent::__construct($compiler, $ruleFiles);
        $this->parser = $parser;
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
                $rules = array_merge(
                    $rules,
                    $this->parser->parse($file)
                );
            }

            $this->compiledRules = $this->compiler->compile($rules);
        }

        return $this->compiledRules;
    }
}

class_alias(DefinitionBased::class, 'eZ\Publish\Core\Persistence\TransformationProcessor\DefinitionBased');
