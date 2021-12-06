<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\UrlRedecoratorInterface;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use SimpleXMLElement;

class ImageConverter extends BinaryFileConverter
{
    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $imageIoService;

    /** @var \Ibexa\Core\IO\UrlRedecoratorInterface */
    private $urlRedecorator;

    public function __construct(IOServiceInterface $imageIoService, UrlRedecoratorInterface $urlRedecorator)
    {
        $this->imageIoService = $imageIoService;
        $this->urlRedecorator = $urlRedecorator;
    }

    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $value
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        if (isset($value->data)) {
            // Determine what needs to be stored
            if (isset($value->data['width']) && isset($value->data['fieldId'])) {
                // width + field id set means that something really needs to be stored
                $storageFieldValue->dataText = $this->createLegacyXml($value->data);
            } elseif (isset($value->data['fieldId'])) {
                // $fieldId without width mleans an empty field
                $storageFieldValue->dataText = $this->createEmptyLegacyXml($value->data);
            }
            // otherwise the image is unprocessed and the DB field stays empty
            // there will be a subsequent call to this method, after the image
            // has been stored
        }
    }

    /**
     * Creates an XML considered "empty" by the legacy storage.
     *
     * @param array $contentMetaData
     *
     * @return string
     */
    protected function createEmptyLegacyXml($contentMetaData)
    {
        return $this->fillXml(
            array_merge(
                [
                    'uri' => '',
                    'path' => '',
                    'width' => '',
                    'height' => '',
                    'mime' => '',
                    'alternativeText' => '',
                    'additionalData' => [],
                ],
                $contentMetaData
            ),
            [
                'basename' => '',
                'extension' => '',
                'dirname' => '',
                'filename' => '',
            ],
            time()
        );
    }

    /**
     * Returns the XML required by the legacy database.
     *
     * @param array $data
     *
     * @return string
     */
    protected function createLegacyXml(array $data)
    {
        $data['uri'] = $this->urlRedecorator->redecorateFromSource($data['uri']);
        $pathInfo = pathinfo($data['uri']);

        return $this->fillXml($data, $pathInfo, time());
    }

    /**
     * Fill the XML template with the data provided.
     *
     * @param array $imageData
     * @param array $pathInfo
     * @param int $timestamp
     *
     * @return string
     */
    protected function fillXml($imageData, $pathInfo, $timestamp)
    {
        $additionalData = $this->buildAdditionalDataTag($imageData['additionalData'] ?? []);

        $xml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="%s" filename="%s"
    suffix="%s" basename="%s" dirpath="%s" url="%s"
    original_filename="%s" mime_type="%s" width="%s"
    height="%s" alternative_text="%s" alias_key="%s" timestamp="%s">
  <original attribute_id="%s" attribute_version="%s" attribute_language="%s"/>
  <information Height="%s" Width="%s" IsColor="%s"/>
  {$additionalData}
</ezimage>
EOT;

        return sprintf(
            $xml,
            // <ezimage>
            ($pathInfo['basename'] !== '' ? '1' : ''), // is_valid="%s"
            htmlspecialchars($pathInfo['basename']), // filename="%s"
            htmlspecialchars($pathInfo['extension']), // suffix="%s"
            htmlspecialchars($pathInfo['filename']), // basename="%s"
            htmlspecialchars($pathInfo['dirname']), // dirpath
            htmlspecialchars($imageData['uri']), // url
            htmlspecialchars($pathInfo['basename']), // @todo: Needs original file name, for whatever reason?
            htmlspecialchars($imageData['mime']), // mime_type
            htmlspecialchars($imageData['width']), // width
            htmlspecialchars($imageData['height']), // height
            htmlspecialchars($imageData['alternativeText']), // alternative_text
            htmlspecialchars(1293033771), // alias_key, fixed for the original image
            htmlspecialchars($timestamp), // timestamp
            // <original>
            $imageData['fieldId'],
            $imageData['versionNo'],
            $imageData['languageCode'],
            // <information>
            $imageData['height'], // Height
            $imageData['width'], // Width
            1// IsColor @todo Do we need to fix that here?
        );
    }

    private function buildAdditionalDataTag(array $imageEditorData): string
    {
        $xml = new SimpleXMLElement('<additional_data/>');
        foreach ($imageEditorData as $option => $value) {
            $xml->addChild('attribute', (string) $value)->addAttribute('key', $option);
        }

        // Cutout xml header
        $dom = dom_import_simplexml($xml);

        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        if (empty($value->dataText)) {
            // Special case for anonymous user
            return;
        }
        $fieldValue->data = $this->parseLegacyXml($value->dataText);
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
    {
        $validators = $fieldDef->fieldTypeConstraints->validators;

        $storageDef->dataInt1 = $validators['FileSizeValidator']['maxFileSize'] ?? 0;
        $storageDef->dataInt2 = (int)($validators['AlternativeTextValidator']['required'] ?? 0);
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
        $fieldDef->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'validators' => [
                    'FileSizeValidator' => [
                        'maxFileSize' => $storageDef->dataInt1 !== 0 ? $storageDef->dataInt1 : null,
                    ],
                    'AlternativeTextValidator' => [
                        'required' => (bool)$storageDef->dataInt2,
                    ],
                ],
            ]
        );
    }

    /**
     * Parses the XML from the legacy database.
     *
     * Returns only the data required by the FieldType, nothing more.
     *
     * @param string $xml
     *
     * @return array
     */
    protected function parseLegacyXml($xml)
    {
        $extractedData = [];

        $dom = new \DOMDocument();
        $dom->loadXml($xml);

        $ezimageTag = $dom->documentElement;

        if (!$ezimageTag->hasAttribute('url')) {
            throw new \RuntimeException('Missing attribute "url" in the <ezimage/> tag.');
        }

        if (($legacyUrl = $ezimageTag->getAttribute('url')) === '') {
            // Detected XML considered "empty" by the legacy storage
            return null;
        }

        $url = $this->urlRedecorator->redecorateFromTarget($legacyUrl);
        $extractedData['id'] = $this->imageIoService->loadBinaryFileByUri($url)->id;

        if (!$ezimageTag->hasAttribute('filename')) {
            throw new \RuntimeException('Missing attribute "filename" in the <ezimage/> tag.');
        }
        $extractedData['fileName'] = $ezimageTag->getAttribute('filename');
        $extractedData['width'] = $ezimageTag->getAttribute('width');
        $extractedData['height'] = $ezimageTag->getAttribute('height');
        $extractedData['mime'] = $ezimageTag->getAttribute('mime_type');

        if (!$ezimageTag->hasAttribute('alternative_text')) {
            throw new \RuntimeException('Missing attribute "alternative_text" in the <ezimage/> tag.');
        }
        $extractedData['alternativeText'] = $ezimageTag->getAttribute('alternative_text');

        $extractedData['additionalData'] = [];
        $additionalDataTagList = $dom->getElementsByTagName('additional_data');

        /** @var \DOMElement $additionalDataElement */
        foreach ($additionalDataTagList as $additionalDataElement) {
            /** @var \DOMElement $datum */
            foreach ($additionalDataElement->getElementsByTagName('attribute') as $datum) {
                /** @var \DOMNamedNodeMap $option */
                $option = $datum->attributes;
                $extractedData['additionalData'][$option->getNamedItem('key')->nodeValue] = $datum->nodeValue;
            }
        }

        return $extractedData;
    }
}

class_alias(ImageConverter::class, 'eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter');
