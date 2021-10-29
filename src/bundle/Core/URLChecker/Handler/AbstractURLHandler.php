<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker\Handler;

use DateTime;
use Exception;
use Ibexa\Bundle\Core\URLChecker\URLHandlerInterface;
use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractURLHandler implements URLHandlerInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\URLService */
    protected $urlService;

    public function __construct(URLService $urlService)
    {
        $this->logger = new NullLogger();
        $this->urlService = $urlService;
    }

    abstract protected function getOptionsResolver(): OptionsResolver;

    abstract public function getOptions(): array;

    /**
     * Sets URL status.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\URL $url
     * @param bool $isValid
     */
    protected function setUrlStatus(URL $url, $isValid)
    {
        try {
            $updateStruct = $this->urlService->createUpdateStruct();
            $updateStruct->isValid = $isValid;
            $updateStruct->lastChecked = new DateTime();

            $this->urlService->updateUrl($url, $updateStruct);

            $this->logger->info(sprintf('URL id = %d (%s) was checked (valid = %s)', $url->id, $url->url, (int) $isValid));
        } catch (Exception $e) {
            $this->logger->error(sprintf('Cannot update URL id = %d status: %s', $url->id, $url->url));
        }
    }
}

class_alias(AbstractURLHandler::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\Handler\AbstractURLHandler');
