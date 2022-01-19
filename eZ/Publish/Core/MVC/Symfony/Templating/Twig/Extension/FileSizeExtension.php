<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Locale;
use NumberFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class FileSizeExtension.
 */
class FileSizeExtension extends AbstractExtension
{
    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @param array $suffixes
     */
    protected $suffixes;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    protected $configResolver;

    /**
     * @param  \eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface $localeConverter
     */
    protected $localeConverter;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface $localeConverter
     * @param array $suffixes
     */
    public function __construct(TranslatorInterface $translator, array $suffixes, ConfigResolverInterface $configResolver, LocaleConverterInterface $localeConverter)
    {
        $this->translator = $translator;
        $this->suffixes = $suffixes;
        $this->configResolver = $configResolver;
        $this->localeConverter = $localeConverter;
    }

    private function getLocale()
    {
        foreach ($this->configResolver->getParameter('languages') as $locale) {
            $convertedLocale = $this->localeConverter->convertToPOSIX($locale);
            if ($convertedLocale !== null) {
                return $convertedLocale;
            }
        }

        return Locale::getDefault();
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return [
            new TwigFilter('ez_file_size', [$this, 'sizeFilter']),
        ];
    }

    /**
     * Returns the binary file size, $precision will determine the decimal number precision,
     * and the Locale will alter the format of the result by choosing between coma or point pattern.
     *
     * @param int $number
     * @param int $precision
     *
     * @return string
     */
    public function sizeFilter($number, $precision)
    {
        $mod = 1024;
        $index = count($this->suffixes);
        if ($number < ($mod ** $index)) {
            for ($i = 0; $number >= $mod; ++$i) {
                $number /= $mod;
            }
        } else {
            $number /= $mod ** ($index - 1);
            $i = ($index - 1);
        }
        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::PATTERN_DECIMAL);
        $formatter->setPattern($formatter->getPattern() . ' ' . $this->translator->trans($this->suffixes[$i]));
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);

        return $formatter->format($number);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ezpublish.fileSize';
    }
}
