/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');
require('jquery-ui/themes/base/all.css')

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
var $ = require('jquery');

require('jquery-ui/ui/core');
require('foundation-sites');
require('foundation-sites/js/foundation');

$(function() {
    $(document).foundation();
})
