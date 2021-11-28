<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker\Handler;

use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailToHandler extends AbstractConfigResolverBasedURLHandler
{
    public const MAILTO_PATTERN = '/^mailto:(.+)@([^?]+)(\\?.*)?$/';

    public function __construct(
        URLService $urlService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($urlService, $configResolver, 'url_handler.mailto.options');
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $urls)
    {
        $options = $this->getOptions();

        if (!$options['enabled']) {
            return;
        }

        foreach ($urls as $url) {
            if (preg_match(self::MAILTO_PATTERN, $url->url, $matches)) {
                $host = trim($matches[2]);

                $this->setUrlStatus($url, checkdnsrr($host, 'MX'));
            }
        }
    }

    protected function getOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'enabled' => true,
        ]);

        $resolver->setAllowedTypes('enabled', 'bool');

        return $resolver;
    }
}

class_alias(MailToHandler::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\Handler\MailToHandler');
