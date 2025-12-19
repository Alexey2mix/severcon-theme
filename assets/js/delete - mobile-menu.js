/**
 * Мобильное меню
 */
jQuery(document).ready(function($) {
    'use strict';
    
    var mobileToggle = $('.mobile-menu-toggle');
    var navMenu = $('.nav-menu');
    
    // Открытие/закрытие мобильного меню
    mobileToggle.on('click', function() {
        $(this).toggleClass('active');
        navMenu.toggleClass('active');
        $('body').toggleClass('menu-open');
    });
    
    // Закрытие меню при клике на ссылку
    navMenu.find('a').on('click', function() {
        mobileToggle.removeClass('active');
        navMenu.removeClass('active');
        $('body').removeClass('menu-open');
    });
    
    // Закрытие меню при клике вне его
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.nav-container').length) {
            mobileToggle.removeClass('active');
            navMenu.removeClass('active');
            $('body').removeClass('menu-open');
        }
    });
    
    // Обработка клавиши Escape
    $(document).on('keyup', function(e) {
        if (e.keyCode === 27) {
            mobileToggle.removeClass('active');
            navMenu.removeClass('active');
            $('body').removeClass('menu-open');
        }
    });
});