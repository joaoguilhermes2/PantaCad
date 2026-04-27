(function () {
    const notifications = Array.from(document.querySelectorAll('[data-notification]'));

    function hideNotification(element) {
        if (!element) {
            return;
        }

        element.classList.add('is-hiding');

        window.setTimeout(function () {
            element.remove();
        }, 180);
    }

    notifications.forEach(function (notification) {
        const closeButton = notification.querySelector('[data-dismiss-notification]');

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                hideNotification(notification);
            });
        }

        window.setTimeout(function () {
            notification.remove();
        }, 5200);
    });
}());