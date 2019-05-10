<?php

namespace FH\PostcodeAPI\Exception;

/**
 * @author Vlad Shut <vladyslav.shut@gmail.com>
 */
class InvalidUrlException extends \Exception implements PostcodeApiExceptionInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        parent::__construct("Invalid url provided '$url'");

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
