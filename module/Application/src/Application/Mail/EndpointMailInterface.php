<?php

namespace Application\Mail;


interface EndpointMailInterface {
    public function send($subject, $body);
    public function setSubject($subject);
    public function setBody($body);
}