import { Controller } from '@hotwired/stimulus';

/*
 * Voici un exemple de contrôleur Stimulus !
 *
 * Tout élément possédant un attribut data-controller="hello" déclenchera
 * l'exécution de ce contrôleur.
 * Le nom « hello » provient du nom du fichier : hello_controller.js -> « hello »
 *
 * Supprimez ce fichier ou adaptez-le à vos besoins !
 */
export default class extends Controller {
    connect() {
        this.element.textContent = 'Bonjour Stimulus ! Modifiez-moi dans assets/controllers/hello_controller.js';
    }
}
