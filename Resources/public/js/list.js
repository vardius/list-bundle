/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function () {
    $('.list-toggle').click(function () {
        $('.list-filters').toggle();
        $('.list-content')
            .toggleClass('col-md-12')
            .toggleClass('col-md-8');
        $('.list-navigation')
            .toggleClass('col-md-12')
            .toggleClass('col-md-8');
    });
});