<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Translation;

use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Psr\Log\LoggerInterface;
use Twig\Node\Node as TwigNode;

/**
 * Visits calls to some known translatable exceptions, into the repository_exceptions domain.
 */
class ValidationErrorFileVisitor implements LoggerAwareInterface, FileVisitorInterface, NodeVisitor
{
    /** @var \JMS\TranslationBundle\Translation\FileSourceFactory */
    private $fileSourceFactory;

    /** @var \PhpParser\NodeTraverser */
    private $traverser;

    /** @var \JMS\TranslationBundle\Model\MessageCatalogue */
    private $catalogue;

    /** @var \SplFileInfo */
    private $file;

    /** @var \Doctrine\Common\Annotations\DocParser */
    private $docParser;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PhpParser\Node */
    private $previousNode;

    /** @var string */
    protected $defaultDomain = 'repository_exceptions';

    /**
     * Methods and "domain" parameter offset to extract from PHP code.
     *
     * @var array method => position of the "domain" parameter
     */
    protected $classToExtractFrom = [
        'contentvalidationexception',
        'forbiddenexception',
    ];

    /**
     * DefaultPhpFileExtractor constructor.
     *
     * @param \Doctrine\Common\Annotations\DocParser $docParser
     * @param \JMS\TranslationBundle\Translation\FileSourceFactory $fileSourceFactory
     */
    public function __construct(DocParser $docParser, FileSourceFactory $fileSourceFactory)
    {
        $this->docParser = $docParser;
        $this->fileSourceFactory = $fileSourceFactory;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \PhpParser\Node $node
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\New_
            || !is_string($node->class)
            || strtolower($node->class) !== 'validationerror') {
            $this->previousNode = $node;

            return;
        }

        $ignore = false;
        $desc = $meaning = null;
        if (null !== $docComment = $this->getDocCommentForNode($node)) {
            if ($docComment instanceof Doc) {
                $docComment = $docComment->getText();
            }
            foreach ($this->docParser->parse($docComment, 'file ' . $this->file . ' near line ' . $node->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } elseif ($annot instanceof Desc) {
                    $desc = $annot->text;
                } elseif ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        if (!$node->args[0]->value instanceof String_) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Can only extract the translation ID from a scalar string, but got "%s". Refactor your code to make it extractable, or add the doc comment /** @Ignore */ to this code element (in %s on line %d).', get_class($node->args[0]->value), $this->file, $node->args[0]->value->getLine());

            if ($this->logger) {
                $this->logger->error($message);

                return;
            }

            throw new RuntimeException($message);
        }

        $message = new Message($node->args[0]->value->value, $this->defaultDomain);
        $message->setDesc($desc);
        $message->setMeaning($meaning);
        $message->addSource($this->fileSourceFactory->create($this->file, $node->getLine()));
        $this->catalogue->add($message);

        // plural
        if ($node->args[1]->value instanceof String_) {
            $message = new Message($node->args[1]->value->value, $this->defaultDomain);
            $message->setDesc($desc);
            $message->setMeaning($meaning);
            $message->addSource($this->fileSourceFactory->create($this->file, $node->getLine()));
            $this->catalogue->add($message);
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
    }

    /**
     * @param \PhpParser\Node $node
     */
    public function leaveNode(Node $node)
    {
    }

    /**
     * @param array $nodes
     */
    public function afterTraverse(array $nodes)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param \Twig\Node\Node $ast
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, TwigNode $ast)
    {
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return string|null
     */
    private function getDocCommentForNode(Node $node)
    {
        // check if there is a doc comment for the ID argument
        // ->trans(/** @Desc("FOO") */ 'my.id')
        if (null !== $comment = $node->args[0]->getDocComment()) {
            return $comment->getText();
        }

        // this may be placed somewhere up in the hierarchy,
        // -> /** @Desc("FOO") */ trans('my.id')
        // /** @Desc("FOO") */ ->trans('my.id')
        // /** @Desc("FOO") */ $translator->trans('my.id')
        if (null !== $comment = $node->getDocComment()) {
            return $comment->getText();
        } elseif (null !== $this->previousNode && $this->previousNode->getDocComment() !== null) {
            $comment = $this->previousNode->getDocComment();

            return is_object($comment) ? $comment->getText() : $comment;
        }

        return null;
    }
}

class_alias(ValidationErrorFileVisitor::class, 'eZ\Publish\Core\MVC\Symfony\Translation\ValidationErrorFileVisitor');
