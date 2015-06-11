<?php

namespace FH\PostcodeAPIClient\Command;

use Guzzle\Service\Command\AbstractCommand;

/**
 * Command to retrieve geo-information for a given postal code and housenumber (optional).
 *
 * @author Evert Harmeling <evert.harmeling@freshheads.com>
 */
abstract class BaseApiCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        $result = parent::getResult();

        return $result['resource'];
    }
}
