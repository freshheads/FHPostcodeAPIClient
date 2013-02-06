<?php

namespace FH\PostcodeAPIClient\Command;

use Guzzle\Service\Command\AbstractCommand;

/**
 * Command to retrieve geo-information for a given postal code and housenumber (optional).
 *
 * @author Joost Farla <joost.farla@freshheads.com>
 */
class FindPostalCode extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        $this->set(self::RESPONSE_PROCESSING, self::TYPE_MODEL);

        $this->request = $this->client->get();
        $url = $this->request->getUrl(true)->addPath($this->get('postal_code'));

        if ($this->get('house_number') !== null)
        {
            $url->addPath($this->get('house_number'));
        }

        $this->request->setUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        $result = parent::getResult();

        return $result['resource'];
    }
}
