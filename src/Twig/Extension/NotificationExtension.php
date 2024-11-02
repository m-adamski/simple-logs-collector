<?php

namespace App\Twig\Extension;

use App\Helper\NotificationHelper;
use App\Model\Notification\Notification;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension {
    public function __construct(
        private readonly NotificationHelper $notificationHelper,
    ) {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction("notification", [$this, "renderNotification"], ["is_safe" => ["html"], "needs_environment" => true])
        ];
    }

    public function renderNotification(Environment $twig): ?string {

        if (null !== ($notification = $this->notificationHelper->get())) {
            return $twig->render("parts/notification.html.twig", ["notification" => $notification]);
        }

        return null;
    }
}
