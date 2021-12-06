<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\TargetAwareType as SPITargetAwareLimitationType;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIPersistenceContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as SPIPersistenceLanguageHandler;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo as SPIVersionInfo;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LanguageLimitation as APILanguageLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;

/**
 * LanguageLimitation is a Content limitation.
 */
class LanguageLimitationType implements SPITargetAwareLimitationType
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\Language\Handler */
    private $persistenceLanguageHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $persistenceContentHandler;

    /** @var \Ibexa\Core\Limitation\LanguageLimitation\VersionTargetEvaluator[] */
    private $versionTargetEvaluators;

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language\Handler $persistenceLanguageHandler
     * @param \Ibexa\Contracts\Core\Persistence\Content\Handler $persistenceContentHandler
     * @param \Ibexa\Core\Limitation\LanguageLimitation\VersionTargetEvaluator[] $versionTargetEvaluators
     */
    public function __construct(
        SPIPersistenceLanguageHandler $persistenceLanguageHandler,
        SPIPersistenceContentHandler $persistenceContentHandler,
        iterable $versionTargetEvaluators
    ) {
        $this->persistenceLanguageHandler = $persistenceLanguageHandler;
        $this->persistenceContentHandler = $persistenceContentHandler;
        $this->versionTargetEvaluators = $versionTargetEvaluators;
    }

    /**
     * Accepts a Limitation value and checks for structural validity.
     *
     * Makes sure LimitationValue object and ->limitationValues is of correct type.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitationValue
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected type/structure
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof APILanguageLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                APILanguageLimitation::class,
                $limitationValue
            );
        } elseif (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentType(
                    "\$limitationValue->limitationValues[{$key}]",
                    'string',
                    $value
                );
            }
        }
    }

    /**
     * Makes sure every language code defined as limitation exists.
     *
     * Make sure {@link acceptValue()} is checked first!
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitationValue
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];
        $existingLanguages = $this->persistenceLanguageHandler->loadListByLanguageCodes(
            $limitationValue->limitationValues
        );
        $missingLanguages = array_diff(
            $limitationValue->limitationValues,
            array_keys($existingLanguages)
        );
        if (!empty($missingLanguages)) {
            $validationErrors[] = new ValidationError(
                "limitationValues[] => '%languageCodes%' translation(s) do not exist",
                null,
                [
                    'languageCodes' => implode(', ', $missingLanguages),
                ]
            );
        }

        return $validationErrors;
    }

    /**
     * Create the Limitation Value.
     *
     * @param array[] $limitationValues
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation
     */
    public function buildValue(array $limitationValues): APILimitationValue
    {
        return new APILanguageLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * Evaluate permission against content & target.
     *
     * {@inheritdoc}
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        ValueObject $object,
        array $targets = null
    ): ?bool {
        if (null == $targets) {
            $targets = [];
        }

        // the main focus here is an intent to update to a new Version
        foreach ($targets as $target) {
            if (!$target instanceof Target\Version) {
                continue;
            }

            $accessVote = $this->evaluateVersionTarget($target, $value);

            // continue evaluation of targets if there was no explicit grant/deny
            if ($accessVote === self::ACCESS_ABSTAIN) {
                continue;
            }

            return $accessVote;
        }

        // in other cases we need to evaluate object
        return $this->evaluateObject($object, $value);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     *
     * @return bool|null
     */
    private function evaluateObject(ValueObject $object, APILimitationValue $value): ?bool
    {
        // by default abstain from making decision for unknown object
        $accessVote = self::ACCESS_ABSTAIN;

        // load for evaluation VersionInfo for Content & ContentInfo objects
        if ($object instanceof Content) {
            $object = $object->getVersionInfo();
        } elseif ($object instanceof ContentInfo) {
            try {
                $object = $this->persistenceContentHandler->loadVersionInfo(
                    $object->id,
                    $object->currentVersionNo
                );
            } catch (NotFoundException $e) {
                return self::ACCESS_DENIED;
            }
        }

        // cover creating Content Draft for new Content item
        if ($object instanceof ContentCreateStruct) {
            $accessVote = $this->evaluateContentCreateStruct($object, $value);
        } elseif ($object instanceof VersionInfo || $object instanceof SPIVersionInfo) {
            $accessVote = in_array($object->initialLanguageCode, $value->limitationValues)
                ? self::ACCESS_GRANTED
                : self::ACCESS_DENIED;
        }

        return $accessVote;
    }

    /**
     * Evaluate language codes of allowed translations for ContentCreateStruct.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $object
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     *
     * @return bool|null
     */
    private function evaluateContentCreateStruct(
        ContentCreateStruct $object,
        APILimitationValue $value
    ): ?bool {
        $languageCodes = $this->getAllLanguageCodesFromCreateStruct($object);

        // check if object contains only allowed language codes
        return empty(array_diff($languageCodes, $value->limitationValues))
            ? self::ACCESS_GRANTED
            : self::ACCESS_DENIED;
    }

    /**
     * Evaluate permissions to create new Version.
     *
     * @param \Ibexa\Contracts\Core\Limitation\Target\Version $version
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     *
     * @return bool|null
     */
    private function evaluateVersionTarget(
        Target\Version $version,
        APILimitationValue $value
    ): ?bool {
        $accessVote = self::ACCESS_ABSTAIN;

        foreach ($this->versionTargetEvaluators as $evaluator) {
            if ($evaluator->accept($version)) {
                $accessVote = $evaluator->evaluate($version, $value);
                if ($accessVote === self::ACCESS_DENIED) {
                    return $accessVote;
                }
            }
        }

        return $accessVote;
    }

    /**
     * Get unique list of language codes for all used translations, including mainLanguageCode.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct
     *
     * @return string[]
     */
    private function getAllLanguageCodesFromCreateStruct(
        ContentCreateStruct $contentCreateStruct
    ): array {
        $languageCodes = [$contentCreateStruct->mainLanguageCode];
        foreach ($contentCreateStruct->fields as $field) {
            $languageCodes[] = $field->languageCode;
        }

        return array_unique($languageCodes);
    }

    /**
     * Returns Criterion for use in find() query.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $value
     * @param \Ibexa\Contracts\Core\Repository\Values\User\UserReference $currentUser
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function getCriterion(
        APILimitationValue $value,
        APIUserReference $currentUser
    ): CriterionInterface {
        if (empty($value->limitationValues)) {
            // A Policy should not have empty limitationValues stored
            throw new BadStateException(
                '$value',
                '$value->limitationValues is empty'
            );
        }

        // several limitation values: IN operation
        return new Criterion\LanguageCode($value->limitationValues);
    }

    /**
     * For LanguageLimitationType it returns an empty array because schema is not deterministic.
     *
     * @see validate for business logic.
     */
    public function valueSchema(): array
    {
        return [];
    }
}

class_alias(LanguageLimitationType::class, 'eZ\Publish\Core\Limitation\LanguageLimitationType');
