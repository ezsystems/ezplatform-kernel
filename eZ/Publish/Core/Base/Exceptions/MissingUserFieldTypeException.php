<?php

namespace eZ\Publish\Core\Base\Exceptions;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;

class MissingUserFieldTypeException extends ContentValidationException
{
    public function __construct(ContentType $contentType)
    {
        parent::__construct(
            'The provided Content Type "%contentType%" does not contain the ezuser Field Type',
            [
                'contentType' => $contentType->identifier,
            ]
        );
    }
}