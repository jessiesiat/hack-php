<?php
 
namespace Hack\EventListener;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
 
class StringResponseListener implements EventSubscriberInterface
{
    /**
     * Handles the event. Transform the raw response to a Response object
     * if it is not. 
     *
     * @param  \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @return void
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
 
        if (is_string($response)) {
            $event->setResponse(new Response($response));
        }
    }
 
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array('kernel.view' => 'onView');
    }
}