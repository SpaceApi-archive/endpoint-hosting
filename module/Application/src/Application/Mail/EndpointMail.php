<?php

namespace Application\Mail;

use Zend\Mail;

class EndpointMail extends Mail\Message implements EndpointMailInterface
{
    protected $transport;
    protected $recipients;

    public function __construct() {
        $this->setFrom('no-reply@spaceapi.net');
        $this->transport = new Mail\Transport\Sendmail();
    }

    /**
     * Sends emails to spaceapi developers & contributors.
     *
     * @param $subject
     * @param $body
     */
    public function send($subject = '', $body = '') {

        if(empty($this->recipients))
            return;

        if (!empty($subject))
            $this->setSubject($subject);

        if (!empty($body))
            $this->setBody($body);

        if (is_array($this->recipients)) {
            foreach($this->recipients as $email) {
                $this->addTo($email);
                $this->transport->send($this);
            }
        } else {
            $this->addTo($this->recipients);
            $this->transport->send($this);
        }
    }

    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }
}