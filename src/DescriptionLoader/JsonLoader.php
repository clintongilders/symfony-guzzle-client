<?php

/*
 * This file is part of the Guzzle description loader package.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DescriptionLoader;

use App\DescriptionLoader\FileLoader;

class JsonLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    public function loadResource(mixed $resource): mixed
    {
        if ($data = file_get_contents($resource)) {
            $configValues = json_decode($data, true);
            if (0 < $errorCode = json_last_error()) {
                throw new \Exception(sprintf('Error parsing JSON - %s', $this->getJSONErrorMessage($errorCode)));
            }
        }

        return $configValues??[];
    }

    /**
     * Translates JSON_ERROR_* constant into meaningful message.
     *
     * @param int $errorCode Error code returned by json_last_error() call
     *
     * @return string Message string
     */
    private function getJSONErrorMessage($errorCode)
    {
        return match ($errorCode) {
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default => 'Unknown error',
        };
    }

    public function supports(mixed $resource, mixed $type = null): bool
    {
        return is_string($resource) && 'json' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }
}