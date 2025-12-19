/**
 * Main JavaScript file for Severcon theme
 */

(function($) {
    'use strict';

    /**
     * Document ready
     */
    $(document).ready(function() {
        
        // Мобильное меню
        $('.mobile-menu-toggle').on('click', function(e) {
            e.preventDefault();
            $('.main-navigation').toggleClass('active');
            $(this).toggleClass('active');
        });
        
        // Закрытие мобильного меню при клике на ссылку
        $('.main-navigation a').on('click', function() {
            if ($(window).width() <= 768) {
                $('.main-navigation').removeClass('active');
                $('.mobile-menu-toggle').removeClass('active');
            }
        });
        
        // Выпадающие меню
        $('.menu-item-has-children > a').on('click', function(e) {
            if ($(window).width() <= 768) {
                e.preventDefault();
                $(this).parent().toggleClass('submenu-open');
            }
        });
        
        // Карусель на главной (если есть)
        if ($('.hero-slider').length) {
            $('.hero-slider').slick({
                dots: true,
                arrows: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000
            });
        }
        
        // Фильтры товаров
        $('.product-filter').on('change', function() {
            var filter = $(this).val();
            // Здесь будет код фильтрации
        });
        
        // Модальные окна
        $('[data-modal]').on('click', function(e) {
            e.preventDefault();
            var modalId = $(this).data('modal');
            $('#' + modalId).fadeIn();
        });
        
        $('.modal-close, .modal-overlay').on('click', function() {
            $(this).closest('.modal').fadeOut();
        });
        
        // Плавная прокрутка к якорям
        $('a[href^="#"]').on('click', function(e) {
            if ($(this).attr('href') !== '#') {
                e.preventDefault();
                var target = $(this).attr('href');
                if ($(target).length) {
                    $('html, body').animate({
                        scrollTop: $(target).offset().top - 100
                    }, 600);
                }
            }
        });
        
        // Форма обратной связи
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#contact-form')[0].reset();
                        $('.form-message').html('<div class="alert success">Сообщение отправлено!</div>');
                    } else {
                        $('.form-message').html('<div class="alert error">Ошибка отправки!</div>');
                    }
                }
            });
        });
        
        // Инициализация Load More функционала
        initLoadMore();
        
        // Инициализация корзины
        initCart();
        
        // Автоматическое копирование стилей с кнопки "Заявка на оборудование"
        setTimeout(copyButtonStyles, 1000);
        
    }); // document ready
    
    /**
     * Load More functionality для новостей
     */
    function initLoadMore() {
        var $loadMoreBtn = $('#load-more-news');
        
        if (!$loadMoreBtn.length) return;
        
        $loadMoreBtn.on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $btnText = $button.find('.button-text');
            var $spinner = $button.find('.button-spinner');
            var $messageDiv = $button.siblings('.load-more-message');
            
            var currentPage = parseInt($button.data('page'));
            var maxPages = parseInt($button.data('max-pages'));
            var nextPage = currentPage + 1;
            
            // Показываем состояние загрузки
            $btnText.hide();
            $spinner.show();
            $button.prop('disabled', true).css('opacity', '0.7');
            
            // Собираем данные для AJAX
            var ajaxData = {
                action: 'load_more_news',
                nonce: severcon_ajax.nonce,
                page: nextPage
            };
            
            // Добавляем данные категории/тега если есть
            var category = $button.data('category');
            var tag = $button.data('tag');
            
            if (category) {
                ajaxData.category = category;
            }
            
            if (tag) {
                ajaxData.tag = tag;
            }
            
            // Отправляем AJAX запрос
            $.ajax({
                url: severcon_ajax.ajax_url,
                type: 'POST',
                data: ajaxData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // ДОБАВЛЯЕМ НОВЫЕ ПОСТЫ НА СТРАНИЦУ
                        var $newsGrid = $('#news-grid');
                        if ($newsGrid.length) {
                            // Добавляем HTML с новыми постами
                            $newsGrid.append(response.data.html);
                            
                            // Анимация появления
                            var $newItems = $newsGrid.children('.news-archive-item').slice(-response.data.html.split('news-archive-item').length + 1);
                            $newItems.css({
                                'opacity': '0',
                                'transform': 'translateY(20px)'
                            });
                            
                            setTimeout(function() {
                                $newItems.animate({
                                    'opacity': '1',
                                    'transform': 'translateY(0)'
                                }, 300);
                            }, 50);
                        }
                        
                        // Обновляем номер страницы
                        $button.data('page', nextPage);
                        
                        // Проверяем, остались ли еще страницы
                        if (nextPage >= maxPages) {
                            $button.fadeOut(300);
                            if ($messageDiv.length) {
                                $messageDiv.html('<div class="message-info">Все новости загружены</div>').fadeIn();
                            }
                        } else {
                            // Восстанавливаем кнопку
                            $btnText.show();
                            $spinner.hide();
                            $button.prop('disabled', false).css('opacity', '1');
                        }
                        
                    } else {
                        // Обработка ошибки от сервера
                        $btnText.text('Ошибка загрузки').show();
                        $spinner.hide();
                        $button.prop('disabled', false).css('opacity', '1');
                        
                        if ($messageDiv.length) {
                            $messageDiv.html('<div class="message-error">Ошибка при загрузке</div>').fadeIn();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load more error:', error);
                    
                    $btnText.text('Попробовать еще раз').show();
                    $spinner.hide();
                    $button.prop('disabled', false).css('opacity', '1');
                    
                    if ($messageDiv.length) {
                        $messageDiv.html('<div class="message-warning">Ошибка сети. Попробуйте еще раз.</div>').fadeIn();
                    }
                }
            });
        });
    }
    
    /**
     * Копирование стилей с кнопки "Заявка на оборудование"
     */
    function copyButtonStyles() {
        var $sourceBtn = $('#requestBtn');
        var $targetBtn = $('#load-more-news');
        
        if ($sourceBtn.length && $targetBtn.length) {
            // Получаем вычисленные стили исходной кнопки
            var sourceStyles = window.getComputedStyle($sourceBtn[0]);
            
            // Копируем ВСЕ важные стили
            var stylesToCopy = [
                'background-color', 'color', 'border', 'border-radius',
                'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
                'margin', 'font-size', 'font-weight', 'font-family',
                'text-transform', 'letter-spacing', 'line-height',
                'box-shadow', 'text-shadow', 'cursor',
                'display', 'width', 'height', 'min-width', 'min-height',
                'transition', 'text-decoration', 'outline'
            ];
            
            // Применяем стили к целевой кнопке
            stylesToCopy.forEach(function(style) {
                var value = sourceStyles.getPropertyValue(style);
                if (value && value !== 'none' && value !== '0px') {
                    $targetBtn.css(style, value);
                }
            });
            
            // Копируем классы
            var sourceClasses = $sourceBtn.attr('class');
            if (sourceClasses) {
                $targetBtn.addClass(sourceClasses);
            }
            
            console.log('✅ Стили кнопки скопированы');
        }
    }
    
    /**
     * Функции корзины WooCommerce
     */
    function initCart() {
        // Обновление количества товаров в корзине
        $('.quantity input').on('change', function() {
            var $input = $(this);
            var quantity = $input.val();
            var $form = $input.closest('form.cart');
            
            if (quantity < 1) {
                $input.val(1);
            }
        });
        
        // AJAX добавление в корзину
        $('.add_to_cart_button').on('click', function(e) {
            if ($(this).hasClass('ajax_add_to_cart')) {
                var $button = $(this);
                var productId = $button.data('product_id');
                
                $.ajax({
                    url: wc_add_to_cart_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'woocommerce_add_to_cart',
                        product_id: productId,
                        quantity: 1
                    },
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            // Обновляем счетчик корзины
                            if (response.fragments && response.fragments['div.widget_shopping_cart_content']) {
                                $('.widget_shopping_cart_content').replaceWith(response.fragments['div.widget_shopping_cart_content']);
                            }
                            
                            // Показываем уведомление
                            $button.text('Добавлено!');
                            setTimeout(function() {
                                $button.text('В корзину');
                            }, 2000);
                        }
                    }
                });
                
                e.preventDefault();
            }
        });
        
        // Обновление корзины при изменении количества
        $('body').on('click', '.plus, .minus', function() {
            var $button = $(this);
            var $input = $button.siblings('input.qty');
            var currentVal = parseFloat($input.val());
            var min = parseFloat($input.attr('min'));
            var max = parseFloat($input.attr('max'));
            var step = $input.attr('step') ? parseFloat($input.attr('step')) : 1;
            
            if ($button.hasClass('plus')) {
                if (max && (currentVal >= max)) {
                    $input.val(max);
                } else {
                    $input.val(currentVal + step);
                }
            } else {
                if (min && (currentVal <= min)) {
                    $input.val(min);
                } else if (currentVal > 0) {
                    $input.val(currentVal - step);
                }
            }
            
            $input.trigger('change');
        });
    }
    
    /**
     * Window resize events
     */
    $(window).on('resize', function() {
        // Закрываем субменю на десктопе
        if ($(window).width() > 768) {
            $('.menu-item-has-children').removeClass('submenu-open');
        }
    });
    
    /**
     Window scroll events
     */
    $(window).on('scroll', function() {
        var scrollTop = $(window).scrollTop();
        
        // Фиксированная шапка
        if (scrollTop > 100) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
        
        // Кнопка "Наверх"
        if (scrollTop > 500) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    
    // Кнопка "Наверх"
    $('#back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 600);
    });
    
})(jQuery);

/**
 * Вспомогательные функции вне jQuery wrapper
 */

// Отложенная загрузка изображений (lazy load)
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                
                if (src) {
                    img.src = src;
                    img.classList.add('loaded');
                }
                
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
}

// Проверка поддержки WebP
function checkWebPSupport(callback) {
    var img = new Image();
    img.onload = function() {
        callback(true);
    };
    img.onerror = function() {
        callback(false);
    };
    img.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
}

// Использование WebP если поддерживается
checkWebPSupport(function(isSupported) {
    if (isSupported) {
        document.documentElement.classList.add('webp');
    } else {
        document.documentElement.classList.add('no-webp');
    }
});
