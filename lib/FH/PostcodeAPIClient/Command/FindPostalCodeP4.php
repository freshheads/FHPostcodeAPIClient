<?php

namespace FH\PostcodeAPIClient\Command;

/**
 * Command to retrieve geo-information for a given postal code and housenumber (optional).
 *
 * @author Evert Harmeling <evert.harmeling@freshheads.com>
 */
class FindPostalCodeP4 extends BaseApiCommand
{
    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        $this->request = $this->client->get();
        $url = $this->request->getUrl(true)->addPath($this->get('postal_code'));
        $url->setQuery(array('type' => 'p4'));

        $this->request->setUrl($url);
    }
}
