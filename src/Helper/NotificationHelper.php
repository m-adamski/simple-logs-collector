<?php

namespace App\Helper;

use App\Model\Notification\Notification;
use Symfony\Component\HttpFoundation\RequestStack;

class NotificationHelper {
    const string SESSION_NOTIFICATION_NAMESPACE = "_application.session.notification";

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function set(Notification $notification): void {
        $this->requestStack->getSession()->set(self::SESSION_NOTIFICATION_NAMESPACE, $notification);
    }

    public function get(): ?Notification {
        if (null !== ($notification = $this->requestStack->getSession()->remove(self::SESSION_NOTIFICATION_NAMESPACE))) {
            if ($notification instanceof Notification) {
                return $notification;
            }
        }

        return null;
    }
}
