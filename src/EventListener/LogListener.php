<?php

namespace Hack\EventListener;

use Hack\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class LogListener implements EventSubscriberInterface {
	
	protected $logger;

	public function __construct(LoggerInterface $logger) 
	{
		$this->logger = $logger;
	}

	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();
        $message = sprintf(
            'Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        $this->logger->error($message);
	}

	public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException',
        );
    }

}