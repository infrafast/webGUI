# ESP32 mock API

`index.php` simule l’endpoint POST utilisé par la webApp. Il conserve l’état du
périphérique dans la session PHP du navigateur et reproduit les réponses du
serveur ESP32 (`retrieve`, `set`, `ping`, `deviceConfig`, etc.).

Les données statiques du protocole sont dans [`fixtures.php`](fixtures.php).
Quand une interface, un dataset, un skeleton ou une banque évolue dans le
firmware, mettre à jour ce fichier en même temps que la webApp. La logique de
session et de validation reste dans `index.php`.

Sans commande POST, `index.php` affiche un petit panneau de diagnostic. Ses
commutateurs pilotent `useNetwork`, `cable`, `networkState` et `AP` dans la
session courante. Le bouton de réinitialisation ne modifie aucune autre session.

Pour tester localement depuis la racine de `webGUI` :

```sh
php -S 127.0.0.1:8080
```
