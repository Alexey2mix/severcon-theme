jQuery(document).ready(function($) {
    'use strict';
    
    // Простой слайдер для главной страницы
    function initHomeSlider() {
        const slides = $('.slide');
        let currentSlide = 0;
        
        function showSlide(index) {
            slides.removeClass('active');
            slides.eq(index).addClass('active');
        }
        
        // Автопереключение слайдов
        setInterval(function() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);
    }
    
    // Инициализация слайдера
    if ($('.hero-slider').length) {
        initHomeSlider();
    }
});