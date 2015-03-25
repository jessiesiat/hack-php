<?php

namespace Hack\EventListener;

use Hack\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class LogListener implements EventSubscriberInterface {
	
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * Initialize the logger instance
	 * 
	 * @param  \Psr\Log\LoggerInterface  $logger
	 */
	public function __construct(LoggerInterface $logger) 
	{
		$this->logger = $logger;
	}

	/**
	 * Controller to handle logging when exception occurs
	 * 
	 * @param  \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent  $event
	 * @return void
	 */
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

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException',
        );
    }

}