<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\EventListeners;

use App\Event\PizzaCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Contracts\EventDispatcher\Event;

class PizzaCreatedListener
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var NotifierInterface
     */
    private NotifierInterface $notifier;

    public function __construct(LoggerInterface $logger, NotifierInterface $notifier)
    {
        $this->logger = $logger;
        $this->notifier = $notifier;
    }

    /**
     * @param Event|PizzaCreatedEvent $event
     */
    public function onCreate(Event $event): void
    {
        $this->logger->info(\sprintf('New pizza %s was created!', $event->getPizza()->getTitle()));

        /*
         * Создаем уведомление
         */
        $notification = (new Notification('New pizza!'))->content(\sprintf('New pizza %s was created!', $event->getPizza()->getTitle()));
        /*
         * И посылаем в заранее  сконфигурированный канал
         */
        $this->notifier->send($notification, new AdminRecipient('test@mail.com'));
    }
}
