import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'
import './styles/app.css'; // en dernier pour surcharger le css (si besoin)

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
