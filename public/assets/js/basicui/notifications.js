(function($) {
    "use strict";
    // Default
    $('.default').on('click', function() {
        Snackbar.show({text: 'Hello World! Life is good.', duration: 2000});
    });
// Position
    $('.top-left').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'top-left'
        });
    });
    $('.top-center').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'top-center'
        });
    });
    $('.top-right').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'top-right'
        });
    });
    $('.bottom-left').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'bottom-left'
        });
    });
    $('.bottom-center').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'bottom-center'
        });
    });
    $('.bottom-right').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            pos: 'bottom-right'
        });
    });
// Action Button
    $('.no-action').on('click', function() {
        Snackbar.show({
            showAction: false
        });
    });
// Action Text
    $('.action-text').on('click', function() {
        Snackbar.show({
            actionText: 'Close!'
        });
    });
// Text Color
    $('.text-color').on('click', function() {
        Snackbar.show({
            actionTextColor: '#e91e63',
        });
    });
// Click Callback
    $('.click-callback').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good',
            actionText: 'Close!',
            width: 'auto',
            onActionClick: function(element) {
                //Set opacity of element to 0 to close Snackbar
                $(element).css('opacity', 0);
                Snackbar.show({
                    text: 'Thanks for clicking the  <strong>close</strong>  button!',
                    showActionButton: false
                });
            }
        });
    });
// Duration
    $('.duration').on('click', function() {
        Snackbar.show({
            text: 'Hello World! Life is good for 2 seconds',
            duration: 2000,
        });
    });
// Custom Background
    $('.notification-bg-primary').on('click', function() {
        Snackbar.show({
            text: 'Primary',
            actionTextColor: '#fff',
            backgroundColor: '#2262c6',
            pos: 'bottom-center'
        });
    });
    $('.notification-bg-info').on('click', function() {
        Snackbar.show({
            text: 'Info',
            actionTextColor: '#fff',
            backgroundColor: '#2196f3',
            pos: 'bottom-center'
        });
    });
    $('.notification-bg-success').on('click', function() {
        Snackbar.show({
            text: 'Success',
            actionTextColor: '#fff',
            backgroundColor: '#8dbf42',
            pos: 'bottom-center'
        });
    });
    $('.notification-bg-warning').on('click', function() {
        Snackbar.show({
            text: 'Warning',
            actionTextColor: '#fff',
            backgroundColor: '#e2a03f',
            pos: 'bottom-center'
        });
    });
    $('.notification-bg-danger').on('click', function() {
        Snackbar.show({
            text: 'Danger',
            actionTextColor: '#fff',
            backgroundColor: '#e7515a',
            pos: 'bottom-center'
        });
    });
    $('.notification-bg-dark').on('click', function() {
        Snackbar.show({
            text: 'Dark',
            actionTextColor: '#fff',
            backgroundColor: '#3b3f5c',
            pos: 'bottom-center'
        });
    });
})(jQuery);


