<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProvider;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Ibexa\Core\MVC\Symfony\RequestStackAware;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale view parameter provider.
 */
class LocaleParameterProvider implements ParameterProviderInterface
{
    use RequestStackAware;

    /** @var \Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface */
    protected $localeConverter;

    public function __construct(LocaleConverterInterface $localeConverter)
    {
        $this->localeConverter = $localeConverter;
    }

    /**
     * Returns a hash with 'locale' as key and locale string in POSIX format as value.
     *
     * Locale from request object will be used as locale if set, otherwise field language code
     * will be converted to locale string.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     *
     * @return array
     */
    public function getViewParameters(Field $field)
    {
        $parameters = [];

        $request = $this->getCurrentRequest();
        if ($request && $request->attributes->has('_locale')) {
            $parameters['locale'] = $request->attributes->get('_locale');
        } else {
            $parameters['locale'] = $this->localeConverter->convertToPOSIX($field->languageCode);
        }

        return $parameters;
    }
}

class_alias(LocaleParameterProvider::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProvider\LocaleParameterProvider');
