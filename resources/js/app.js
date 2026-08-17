import './bootstrap';

import Alpine from 'alpinejs';
import { enablePushNotifications } from './push';

window.Alpine = Alpine;
window.enablePushNotifications = enablePushNotifications;

Alpine.start();
