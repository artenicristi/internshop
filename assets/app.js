/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import './styles/front_styles.scss';


// fixme add a system with icons
// require('@fortawesome/fontawesome-free/css/all.min.css');
// require('@fortawesome/fontawesome-free/js/all.js');

// start the Stimulus application
import './bootstrap';
import 'bootstrap/dist/js/bootstrap.min';
import 'popper.js/dist/esm/popper.min';
import 'jquery/dist/jquery.min';
import './widget-bootstrap';

import './order-front';


// font awesome icons
// import 'font-awesome/css/font-awesome.min.css'
