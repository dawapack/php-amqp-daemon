<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

use DaWaPack\Classes\Messages\Exceptions\RequestResponseHeadersException;
use function DaWaPack\Chassis\Helpers\publish;

class Response extends AbstractRequestResponseMessage implements RequestResponseInterface
{
    /**
     * @param int $statusCode
     *
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        $this->headers->application_headers["statusCode"] = $statusCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->headers->application_headers["statusCode"] ?? 0;
    }

    /**
     * @param string $statusMessage
     *
     * @return Response
     */
    public function setStatusMessage(string $statusMessage): Response
    {
        $this->headers->application_headers["statusMessage"] = $statusMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->headers->application_headers["statusMessage"] ?? "";
    }

    /**
     * Send this response through a given channel
     *
     * @param ?string $channelName
     *
     * @return void
     */
    public function send(?string $channelName = null): void
    {
        // do not publish a request built by a consumer
        if (isset($this->consumerTag)) {
            return;
        }
        // check status code
        if (!isset($this->headers->application_headers["statusCode"])) {
            throw new RequestResponseHeadersException("status code of application headers not set");
        }
        // set default status message if not set
        if (!isset($this->headers->application_headers["statusMessage"])) {
            $this->setStatusMessage("");
        }
        // use AMQP default exchange if channel name is not provided
        $channelName = $channelName ?? '';
        // publish the response
        publish($this, $channelName);
    }

}
