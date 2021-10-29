<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\FieldType\User;

use DateTime;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getViewParameters(Field $field): array
    {
        $passwordInfo = $this->userService->getPasswordInfo(
            $this->userService->loadUser($field->value->contentId)
        );

        $passwordExpiresIn = null;
        if (!$passwordInfo->isPasswordExpired() && $passwordInfo->hasExpirationDate()) {
            $passwordExpiresIn = $passwordInfo->getExpirationDate()->diff(new DateTime());
        }

        return [
            'is_password_expired' => $passwordInfo->isPasswordExpired(),
            'password_expires_at' => $passwordInfo->getExpirationDate(),
            'password_expires_in' => $passwordExpiresIn,
        ];
    }
}

class_alias(ParameterProvider::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\User\ParameterProvider');
