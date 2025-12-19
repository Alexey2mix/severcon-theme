jQuery(document).ready(function($) {
    'use strict';
    
    console.log('Severcon theme JS loaded');
    
    // ===== ПЕРЕМЕННЫЕ =====
    const mainHeader = $('#mainHeader');
    const mobileToggle = $('#mobileToggle');
    const mobileMenu = $('#mobileMenu');
    const mobileMenuClose = $('#mobileMenuClose');
    const searchToggle = $('#searchToggle');
    const searchOverlay = $('#searchOverlay');
    const searchClose = $('#searchClose');
    const searchInput = $('#searchOverlay input[type="search"]');
    const requestBtn = $('#requestBtn');
    const requestOverlay = $('#requestOverlay');
    const requestClose = $('#requestClose');
    const requestForm = $('#requestOverlay .request-form');
    
    // ===== ПРИЛИПАЮЩАЯ ШАПКА =====
    function handleStickyHeader() {
        const scrollTop = $(window).scrollTop();
        const headerHeight = mainHeader.outerHeight();
        
        if (scrollTop > 100) {
            mainHeader.addClass('header-sticky');
            $('body').css('padding-top', headerHeight + 'px');
        } else {
            mainHeader.removeClass('header-sticky');
            $('body').css('padding-top', '0');
        }
    }
    
    // ===== МОБИЛЬНОЕ МЕНЮ =====
    function initMobileMenu() {
        mobileToggle.on('click', openMobileMenu);
        mobileMenuClose.on('click', closeMobileMenu);
        mobileMenu.find('a').on('click', closeMobileMenu);
    }
    
    function openMobileMenu() {
        mobileToggle.addClass('active');
        mobileMenu.addClass('active');
        $('body').addClass('menu-open');
    }
    
    function closeMobileMenu() {
        mobileToggle.removeClass('active');
        mobileMenu.removeClass('active');
        $('body').removeClass('menu-open');
    }
    
    // ===== ПОИСК =====
    function initSearch() {
        searchToggle.on('click', openSearch);
        searchClose.on('click', closeSearch);
        
        searchOverlay.on('click', function(e) {
            if (e.target === this) closeSearch();
        });
    }
    
    function openSearch() {
        closeAllModals();
        searchOverlay.addClass('active');
        $('body').addClass('search-open');
        
        setTimeout(() => {
            searchInput.focus();
        }, 300);
    }
    
    function closeSearch() {
        searchOverlay.removeClass('active');
        $('body').removeClass('search-open');
        searchInput.val('');
    }
    
    // ===== ФОРМА ЗАЯВКИ =====
    function initRequestForm() {
        requestBtn.on('click', openRequestForm);
        $('.mobile-request').on('click', function() {
            closeMobileMenu();
            openRequestForm();
        });
        requestClose.on('click', closeRequestForm);
        
        requestOverlay.on('click', function(e) {
            if (e.target === this) closeRequestForm();
        });
        
        requestForm.on('submit', function(e) {
            e.preventDefault();
            alert('Заявка отправлена! Мы свяжемся с вами в ближайшее время.');
            $(this)[0].reset();
            closeRequestForm();
        });
    }
    
    function openRequestForm() {
        closeAllModals();
        requestOverlay.addClass('active');
        $('body').addClass('request-open');
    }
    
    function closeRequestForm() {
        requestOverlay.removeClass('active');
        $('body').removeClass('request-open');
    }
    
    // ===== ESC КЛАВИША =====
    function initEscapeHandler() {
        $(document).on('keyup', function(e) {
            if (e.keyCode === 27) closeAllModals();
        });
    }
    
    function closeAllModals() {
        closeSearch();
        closeRequestForm();
        closeMobileMenu();
        closeQuickView();
    }
    
    // ===== ВЕРТИКАЛЬНЫЙ СЛАЙДЕР =====
    function initVerticalSlider() {
        const slides = $('.slide');
        const navUp = $('.nav-up');
        const navDown = $('.nav-down');
        const currentSlideElement = $('.current-slide');
        let currentSlide = 0;
    
        function updateCounter() {
            const slideNumber = (currentSlide + 1).toString().padStart(2, '0');
            currentSlideElement.text(slideNumber);
        }
    
        function showSlide(index) {
            slides.removeClass('active prev');
            slides.eq(currentSlide).addClass('prev');
            currentSlide = index;
            slides.eq(currentSlide).addClass('active');
            updateCounter();
        }
    
        function nextSlide() {
            let next = currentSlide + 1;
            if (next >= slides.length) next = 0;
            showSlide(next);
        }
    
        function prevSlide() {
            let prev = currentSlide - 1;
            if (prev < 0) prev = slides.length - 1;
            showSlide(prev);
        }
    
        navDown.on('click', nextSlide);
        navUp.on('click', prevSlide);
    
        updateCounter();
    }
    
    // ===== ГАЛЕРЕЯ ТОВАРА =====
    function initProductGallery() {
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.querySelector('.main-product-image img');
        
        if (!thumbnails.length || !mainImage) return;
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const img = this.querySelector('img');
                if (img) {
                    const newSrc = img.src.replace('-150x150', '').replace('-300x300', '');
                    mainImage.src = newSrc;
                }
            });
        });
    }
    
    // ===== КНОПКИ "ЗАПРОСИТЬ ЦЕНУ" =====
    function initProductRequestButtons() {
        // Для кнопок на странице товара
        $(document).on('click', '.request-product-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            handleProductRequest(productId, productName);
        });
        
        // Для кнопок на странице категории
        $(document).on('click', '.request-price-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            handleProductRequest(productId, productName);
        });
    }
    
    function handleProductRequest(productId, productName) {
        openRequestForm();
        
        setTimeout(() => {
            const messageField = $('#request-message');
            if (messageField.length) {
                messageField.val(`Запрос цены на товар: ${productName}`).focus();
            }
        }, 300);
    }
    
    // ===== БЫСТРЫЙ ПРОСМОТР =====
    function initQuickView() {
        $(document).on('click', '.quick-view-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = $(this).data('product-id');
            if (productId) {
                openQuickView(productId);
            }
        });
        
        $('#quickViewClose').on('click', closeQuickView);
        $('#quickViewOverlay').on('click', function(e) {
            if (e.target === this) closeQuickView();
        });
    }
    
    function openQuickView(productId) {
        // Индикатор загрузки
        $('.quick-view-content').html(`
            <div style="padding: 50px; text-align: center;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--accent-color); margin-bottom: 20px;"></i>
                <p>Загрузка товара...</p>
            </div>
        `);
        
        // Открываем overlay
        $('#quickViewOverlay').addClass('active');
        $('body').addClass('modal-open');
        
        // AJAX запрос
        $.ajax({
            url: severcon_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_quick_view',
                product_id: productId,
                nonce: severcon_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    $('.quick-view-content').html(response.data);
                    
                    // Инициализируем кнопки внутри
                    initQuickViewButtons();
                    
                    // Галерея если есть
                    initQuickViewGallery();
                }
            },
            error: function() {
                $('.quick-view-content').html(`
                    <div style="padding: 50px; text-align: center;">
                        <p style="color: #f44336;">Ошибка загрузки</p>
                        <button class="btn btn-primary" onclick="closeQuickView()">Закрыть</button>
                    </div>
                `);
            }
        });
    }
    
    function closeQuickView() {
        $('#quickViewOverlay').removeClass('active');
        $('body').removeClass('modal-open');
    }
    
    // ===== КНОПКИ В БЫСТРОМ ПРОСМОТРЕ =====
    function initQuickViewButtons() {
        // Кнопка "Запросить цену" в быстром просмотре
        $(document).on('click', '.quick-view-request', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            
            closeQuickView();
            setTimeout(() => {
                handleProductRequest(productId, productName);
            }, 300);
        });
        
        // Навигационные стрелки
        $(document).on('click', '.nav-arrow', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            if (productId) {
                openQuickView(productId);
            }
        });
    }
    
    // ===== ГАЛЕРЕЯ В БЫСТРОМ ПРОСМОТРЕ =====
    function initQuickViewGallery() {
        const thumbs = $('.quick-view-thumb');
        const mainImage = $('.quick-view-image-main img');
        
        if (!thumbs.length || !mainImage.length) return;
        
        thumbs.on('click', function() {
            thumbs.removeClass('active');
            $(this).addClass('active');
            
            const img = $(this).find('img');
            if (img.length) {
                const newSrc = img.attr('src').replace('-300x300', '').replace('-150x150', '');
                mainImage.attr('src', newSrc);
            }
        });
    }
    
    // ===== ФИЛЬТРАЦИЯ ТОВАРОВ =====
        // ===== ФИЛЬТРАЦИЯ ТОВАРОВ (полная версия) =====
        function initFilters() {
            const filterBtn = $('.filters-toggle-btn');
            const filterContainer = $('.filters-container');
            const applyBtn = $('.apply-filters');
            const resetBtn = $('.reset-filters');
            const productsGrid = $('.products-grid.category-products-grid');
            const categoryId = productsGrid.data('category-id');
            const activeFiltersContainer = $('#activeFilters');
            
            if (!filterBtn.length || !categoryId) return;
            
            // Переключение видимости фильтров (мобильная версия)
            filterBtn.on('click', function() {
                filterContainer.toggleClass('active');
                $(this).toggleClass('active');
                $(this).find('i').toggleClass('fa-sliders-h fa-times');
            });
            
            // Применение фильтров
            applyBtn.on('click', function() {
                applyFullFilters(categoryId, 1);
            });
            
            // Сброс фильтров
            resetBtn.on('click', function() {
                $('.filter-checkbox').prop('checked', false);
                applyFullFilters(categoryId, 1);
                updateActiveFilters();
            });
            
            // Изменение чекбоксов
            $(document).on('change', '.filter-checkbox', function() {
                updateActiveFilters();
            });
            
            // Сортировка
            $('select.orderby').on('change', function() {
                applyFullFilters(categoryId, 1);
            });
            
            // Инициализация активных фильтров
            updateActiveFilters();
        }
        
        function updateActiveFilters() {
            const activeFiltersContainer = $('#activeFilters');
            const activeFilters = [];
            
            $('.filter-group').each(function() {
                const attribute = $(this).data('attribute');
                const attributeLabel = $(this).find('.filter-group-title').text();
                
                $(this).find('.filter-checkbox:checked').each(function() {
                    const value = $(this).val();
                    const text = $(this).next('.filter-item-text').text();
                    
                    activeFilters.push({
                        attribute: attribute,
                        attributeLabel: attributeLabel,
                        value: value,
                        text: text
                    });
                });
            });
            
            if (activeFilters.length > 0) {
                let html = '<span>Активные фильтры:</span>';
                
                activeFilters.forEach(filter => {
                    html += `
                        <span class="active-filter-tag">
                            ${filter.attributeLabel}: ${filter.text}
                            <button class="remove-filter" 
                                    data-attribute="${filter.attribute}" 
                                    data-value="${filter.value}">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    `;
                });
                
                activeFiltersContainer.html(html);
                
                // Обработка удаления фильтров
                $('.remove-filter').on('click', function(e) {
                    e.preventDefault();
                    const attribute = $(this).data('attribute');
                    const value = $(this).data('value');
                    
                    $(`.filter-group[data-attribute="${attribute}"] .filter-checkbox[value="${value}"]`)
                        .prop('checked', false);
                    
                    const categoryId = $('.products-grid').data('category-id');
                    applyFullFilters(categoryId, 1);
                    updateActiveFilters();
                });
            } else {
                activeFiltersContainer.html('');
            }
        }
        
        function applyFullFilters(categoryId, page = 1) {
            const productsGrid = $('.products-grid.category-products-grid');
            const loader = '<div class="filter-loader"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>';
            
            // Показываем лоадер
            productsGrid.html(loader).addClass('loading');
            
            // Собираем фильтры
            const filters = {};
            $('.filter-group').each(function() {
                const attribute = $(this).data('attribute');
                const selected = [];
                $(this).find('.filter-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                if (selected.length > 0) {
                    filters[attribute] = selected;
                }
            });
            
            // Сортировка
            const orderby = $('select.orderby').val() || 'menu_order';
            
            // AJAX запрос
            $.ajax({
                url: severcon_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'filter_category_products',
                    category_id: categoryId,
                    filters: filters,
                    orderby: orderby,
                    page: page,
                    per_page: 12,
                    nonce: severcon_ajax.filter_nonce
                },
                success: function(response) {
                    productsGrid.removeClass('loading');
                    
                    if (response.success) {
                        productsGrid.html(response.data.html);
                        
                        // Обновляем счетчик
                        $('.woocommerce-result-count').text(`Найдено товаров: ${response.data.count}`);
                        
                        // Обновляем пагинацию
                        updatePagination(response.data.max_pages, page);
                        
                        // Инициализируем кнопки быстрого просмотра
                        initQuickViewButtons();
                    } else {
                        productsGrid.html('<p class="filter-error">Ошибка загрузки товаров</p>');
                    }
                },
                error: function() {
                    productsGrid.html('<p class="filter-error">Ошибка соединения</p>');
                }
            });
        }
        
        function updatePagination(maxPages, currentPage) {
            const paginationContainer = $('.woocommerce-pagination');
            
            if (maxPages <= 1) {
                paginationContainer.hide();
                return;
            }
            
            let paginationHTML = '<ul class="page-numbers">';
            
            // Предыдущая страница
            if (currentPage > 1) {
                paginationHTML += `<li><a class="prev" href="#" data-page="${currentPage - 1}">←</a></li>`;
            }
            
            // Номера страниц
            for (let i = 1; i <= maxPages; i++) {
                if (i == currentPage) {
                    paginationHTML += `<li><span class="current">${i}</span></li>`;
                } else if (i <= 3 || i >= maxPages - 2 || Math.abs(i - currentPage) <= 1) {
                    paginationHTML += `<li><a href="#" data-page="${i}">${i}</a></li>`;
                } else if (i == 4 && maxPages > 7) {
                    paginationHTML += `<li><span class="dots">…</span></li>`;
                }
            }
            
            // Следующая страница
            if (currentPage < maxPages) {
                paginationHTML += `<li><a class="next" href="#" data-page="${currentPage + 1}">→</a></li>`;
            }
            
            paginationHTML += '</ul>';
            paginationContainer.html(paginationHTML).show();
            
            // Обработка кликов по пагинации
            paginationContainer.off('click', 'a').on('click', 'a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                const categoryId = $('.products-grid').data('category-id');
                applyFullFilters(categoryId, page);
            });
        }
        
        // УДАЛИТЕ старую функцию initSimpleFilters() из main.js
    
    // ===== КНОПКА "ПОКАЗАТЬ ВСЕ" =====
    function initViewAllButton() {
        $(document).on('click', '.view-all-products a', function(e) {
            const $button = $(this);
            $button.html('<i class="fas fa-spinner fa-spin"></i> Загрузка...');
            $button.prop('disabled', true);
        });
    }
    
    // ===== ОБРАБОТЧИКИ =====
    function initScrollHandlers() {
        $(window).on('scroll', handleStickyHeader);
        $(window).on('resize', function() {
            if ($(window).width() > 900) closeMobileMenu();
        });
    }
    
    // ===== ИНТЕЛЛЕКТУАЛЬНАЯ КОМПАКТНАЯ ФИЛЬТРАЦИЯ =====
    function initCompactFilters() {
        const toggleAllBtn = $('.toggle-all-filters');
        const filtersCollapse = $('.filters-collapse');
        const applyBtn = $('.apply-filters');
        const resetBtn = $('.reset-filters');
        const productsGrid = $('.products-grid.category-products-grid');
        const categoryId = productsGrid.data('category-id');
        
        if (!toggleAllBtn.length || !categoryId) return;
        
        // Переключение всех фильтров
        toggleAllBtn.on('click', function() {
            $(this).toggleClass('active');
            filtersCollapse.toggleClass('active');
            $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');
        });
        
        // Сохраняем оригинальные счетчики
        saveOriginalCounts();
        
        // Переключение групп фильтров
        $(document).on('click', '.filter-group-toggle', function() {
            const target = $(this).data('target');
            const content = $('#' + target);
            
            $(this).toggleClass('active');
            content.slideToggle(200);
            $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');
        });
        
        // Применение фильтров
        applyBtn.on('click', function() {
            applySmartFilters(categoryId, 1);
        });
        
        // Сброс фильтров
        // В функции initCompactFilters():
        resetBtn.on('click', function(e) {
            e.preventDefault();
            resetAllFilters();
        });
        
        // Изменение чекбоксов
        $(document).on('change', '.filter-checkbox', function() {
            const item = $(this).closest('.filter-item');
            if ($(this).is(':checked')) {
                item.addClass('selected');
            } else {
                item.removeClass('selected');
            }
            updateActiveFilterTags();
        });
        
        // Сортировка
        $('select.orderby').on('change', function() {
            applySmartFilters(categoryId, 1);
        });
        
        // Инициализация
        updateActiveFilterTags();
    }
    
    // ===== СОХРАНЕНИЕ ОРИГИНАЛЬНЫХ СЧЕТЧИКОВ =====
    function saveOriginalCounts() {
        $('.filter-item').each(function() {
            const item = $(this);
            const countText = item.find('.filter-item-count').text();
            const countMatch = countText.match(/\((\d+)\)/);
            
            if (countMatch && countMatch[1]) {
                item.data('original-count', parseInt(countMatch[1]));
            }
        });
        console.log('Original counts saved');
    }
    
    // ===== ОБНОВЛЕНИЕ ТЕГОВ АКТИВНЫХ ФИЛЬТРОВ =====
    function updateActiveFilterTags() {
        let activeFiltersContainer = $('.active-filters-container');
        
        if (!activeFiltersContainer.length) {
            // Создаем контейнер если его нет
            $('.compact-filters').before(`
                <div class="active-filters-container">
                    <div class="active-filters-tags"></div>
                    <button class="clear-all-filters">Очистить все</button>
                </div>
            `);
            activeFiltersContainer = $('.active-filters-container');
            
            // Обработка кнопки "Очистить все"
            activeFiltersContainer.find('.clear-all-filters').on('click', function() {
                $('.filter-checkbox').prop('checked', false);
                $('.filter-item').removeClass('selected');
                const categoryId = $('.products-grid').data('category-id');
                applySmartFilters(categoryId, 1);
            });
        }
        
        const activeFilters = [];
        const activeFilterTags = activeFiltersContainer.find('.active-filters-tags');
        
        $('.compact-filter-group').each(function() {
            const attribute = $(this).data('attribute');
            const attributeLabel = $(this).find('.filter-group-title').text();
            
            $(this).find('.filter-checkbox:checked').each(function() {
                const value = $(this).val();
                const text = $(this).siblings('.filter-item-text').text();
                
                activeFilters.push({
                    attribute: attribute,
                    attributeLabel: attributeLabel,
                    value: value,
                    text: text
                });
            });
        });
        
        if (activeFilters.length > 0) {
            let html = '';
            
            activeFilters.forEach(filter => {
                html += `
                    <span class="active-filter-tag">
                        ${filter.attributeLabel}: ${filter.text}
                        <button class="remove-filter-tag" 
                                data-attribute="${filter.attribute}" 
                                data-value="${filter.value}">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `;
            });
            
            activeFilterTags.html(html);
            activeFiltersContainer.find('.clear-all-filters').show();
            
            // Обработка удаления отдельных фильтров
            activeFiltersContainer.find('.remove-filter-tag').on('click', function(e) {
                e.preventDefault();
                const attribute = $(this).data('attribute');
                const value = $(this).data('value');
                
                $(`.compact-filter-group[data-attribute="${attribute}"] .filter-checkbox[value="${value}"]`)
                    .prop('checked', false)
                    .closest('.filter-item').removeClass('selected');
                
                const categoryId = $('.products-grid').data('category-id');
                applySmartFilters(categoryId, 1);
            });
        } else {
            activeFilterTags.html('');
            activeFiltersContainer.find('.clear-all-filters').hide();
        }
    }

    // ===== УМНАЯ ФИЛЬТРАЦИЯ С ПРАВИЛЬНЫМ СКРЫТИЕМ =====
    function applySmartFilters(categoryId, page = 1) {
        const productsGrid = $('.products-grid.category-products-grid');
        const loader = '<div class="filter-loader"><i class="fas fa-spinner fa-spin"></i> Загрузка...</div>';
        
        productsGrid.html(loader).addClass('loading');
        
        // Собираем активные фильтры
        const activeFilters = {};
        $('.compact-filter-group').each(function() {
            const attribute = $(this).data('attribute');
            const selected = [];
            $(this).find('.filter-checkbox:checked').each(function() {
                selected.push($(this).val());
            });
            if (selected.length > 0) {
                activeFilters['pa_' + attribute] = selected;
            }
        });
        
        // AJAX запрос
        $.ajax({
            url: severcon_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'filter_category_products',
                category_id: categoryId,
                filters: activeFilters,
                orderby: $('select.orderby').val() || 'menu_order',
                page: page,
                per_page: 12,
                nonce: severcon_ajax.filter_nonce
            },
            success: function(response) {
                productsGrid.removeClass('loading');
                
                if (response.success) {
                    productsGrid.html(response.data.html);
                    $('.woocommerce-result-count').text(`Найдено товаров: ${response.data.count}`);
                    
                    // ОБНОВЛЯЕМ ФИЛЬТРЫ - скрываем недоступные
                    updateAndHideUnavailableFilters(categoryId, activeFilters);
                }
            },
            error: function() {
                productsGrid.html('<p class="filter-error">Ошибка загрузки товаров</p>');
            }
        });
    }
    
    // ===== ОБНОВЛЕНИЕ И СКРЫТИЕ НЕДОСТУПНЫХ ФИЛЬТРОВ =====
    function updateAndHideUnavailableFilters(categoryId, activeFilters) {
        console.log('Updating filters to hide unavailable options...');
        
        $.ajax({
            url: severcon_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_filter_counts',
                category_id: categoryId,
                filters: activeFilters,
                nonce: severcon_ajax.filter_nonce
            },
            success: function(response) {
                console.log('Filter update response:', response);
                
                if (response.success && response.data) {
                    applyFilterVisibility(response.data, activeFilters);
                }
            }
        });
    }

    // ===== ПРИМЕНЕНИЕ ВИДИМОСТИ ФИЛЬТРОВ =====
    function applyFilterVisibility(countsData, activeFilters) {
        // Для каждого атрибута в ответе
        $.each(countsData, function(taxonomy, terms) {
            const attribute = taxonomy.replace('pa_', '');
            const group = $(`.compact-filter-group[data-attribute="${attribute}"]`);
            
            if (!group.length) return;
            
            let visibleInGroup = 0;
            
            // Сначала скрываем все элементы группы
            group.find('.filter-item').each(function() {
                const item = $(this);
                const termSlug = item.data('term-slug');
                const checkbox = item.find('.filter-checkbox');
                const isSelected = checkbox.is(':checked');
                
                // Проверяем, доступен ли этот термин
                const isAvailable = terms.hasOwnProperty(termSlug);
                
                if (!isAvailable && !isSelected) {
                    // Недоступный и не выбранный - скрываем полностью
                    item.addClass('filter-unavailable').hide();
                    checkbox.prop('disabled', true);
                } else {
                    // Доступный или выбранный - показываем
                    const count = isAvailable ? terms[termSlug] : 0;
                    item.find('.filter-item-count').text('(' + count + ')');
                    
                    if (count === 0 && !isSelected) {
                        // Доступен, но 0 товаров и не выбран - делаем полупрозрачным
                        item.addClass('filter-zero').css('opacity', '0.5');
                    } else {
                        item.removeClass('filter-zero').css('opacity', '1');
                    }
                    
                    item.removeClass('filter-unavailable').show();
                    checkbox.prop('disabled', false);
                    visibleInGroup++;
                }
            });
            
            // Управляем видимостью всей группы
            if (visibleInGroup === 0) {
                group.addClass('group-empty').slideUp(300);
            } else {
                group.removeClass('group-empty').slideDown(300);
            }
        });
        
        // Скрываем группы без данных
        $('.compact-filter-group').each(function() {
            const attribute = $(this).data('attribute');
            const taxonomy = 'pa_' + attribute;
            
            if (!countsData.hasOwnProperty(taxonomy)) {
                $(this).addClass('group-empty').slideUp(300);
            }
        });
    }
    
   // ===== ПОЛНЫЙ СБРОС ФИЛЬТРОВ =====
    function resetFilters() {
        console.log('Resetting all filters...');
        
        // 1. Сбрасываем все чекбоксы
        $('.filter-checkbox').prop('checked', false);
        $('.filter-item').removeClass('selected');
        
        // 2. Восстанавливаем оригинальные счетчики
        $('.filter-item').each(function() {
            const item = $(this);
            const originalCount = item.data('original-count');
            
            if (originalCount !== undefined) {
                item.find('.filter-item-count').text('(' + originalCount + ')');
            }
            
            // 3. Возвращаем нормальный вид
            item.removeClass('filter-unavailable filter-zero')
                .show()
                .css('opacity', '1')
                .find('.filter-checkbox')
                .prop('disabled', false);
        });
        
        // 4. Показываем все группы
        $('.compact-filter-group').removeClass('group-empty').slideDown(300);
        
        // 5. Обновляем активные теги
        updateActiveFilterTags();
        
        // 6. Применяем фильтрацию (без фильтров)
        const categoryId = $('.products-grid').data('category-id');
        if (categoryId) {
            applySmartFilters(categoryId, 1);
        }
    }
    
// ===== ОБНОВЛЕНИЕ СЧЕТЧИКОВ ФИЛЬТРОВ =====
function updateFilterCounts(categoryId, activeFilters) {
    console.log('Updating filter counts...');
    
    $.ajax({
        url: severcon_ajax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'update_filter_counts',
            category_id: categoryId,
            filters: activeFilters,
            nonce: severcon_ajax.filter_nonce
        },
        success: function(response) {
            console.log('Filter counts response:', response);
            
            if (response.success && response.data) {
                // Обновляем счетчики и скрываем недоступные варианты
                updateFilterDisplay(response.data, activeFilters);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating filter counts:', error);
        }
    });
}

    // ===== ОБНОВЛЕНИЕ ОТОБРАЖЕНИЯ ФИЛЬТРОВ =====
    function updateFilterDisplay(countsData, activeFilters) {
        // Для каждого атрибута
        $.each(countsData, function(taxonomy, terms) {
            const attribute = taxonomy.replace('pa_', '');
            const group = $(`.compact-filter-group[data-attribute="${attribute}"]`);
            
            if (!group.length) return;
            
            // Для каждого термина
            $.each(terms, function(termSlug, count) {
                const item = group.find(`.filter-item[data-term-slug="${termSlug}"]`);
                
                if (!item.length) return;
                
                const countElement = item.find('.filter-item-count');
                const checkbox = item.find('.filter-checkbox');
                
                // Обновляем счетчик
                countElement.text('(' + count + ')');
                
                // Проверяем состояние
                const isSelected = checkbox.is(':checked');
                const isAvailable = count > 0;
                
                if (!isAvailable && !isSelected) {
                    // Недоступный и не выбранный - скрываем
                    item.addClass('hidden-by-filter').hide();
                    checkbox.prop('disabled', true);
                } else if (!isAvailable && isSelected) {
                    // Недоступный но выбранный - оставляем с 0
                    item.removeClass('hidden-by-filter').show();
                    checkbox.prop('disabled', false);
                } else {
                    // Доступный - показываем
                    item.removeClass('hidden-by-filter').show();
                    checkbox.prop('disabled', false);
                }
            });
            
            // Проверяем, нужно ли скрыть всю группу
            const visibleItems = group.find('.filter-item:not(.hidden-by-filter)').length;
            if (visibleItems === 0) {
                group.addClass('all-hidden').hide();
            } else {
                group.removeClass('all-hidden').show();
            }
        });
    }

// ===== ОБНОВЛЕНИЕ ВИДИМОСТИ ГРУПП ФИЛЬТРОВ =====
function updateFilterGroupVisibility(attribute) {
    const group = $(`.compact-filter-group[data-attribute="${attribute}"]`);
    const visibleItems = group.find('.filter-item:not(.hidden-by-filter)').length;
    const totalItems = group.find('.filter-item').length;
    
    if (visibleItems === 0) {
        // Если все варианты скрыты - скрываем всю группу
        group.addClass('all-hidden').slideUp(300);
    } else if (group.hasClass('all-hidden')) {
        // Если появились доступные варианты - показываем группу
        group.removeClass('all-hidden').slideDown(300);
    }
}

    // ===== ОБНОВЛЕНИЕ КНОПОК "ПОКАЗАТЬ ЕЩЕ" =====
    function updateShowMoreButtons() {
        $('.compact-filter-group').each(function() {
            const group = $(this);
            const showMoreBtn = group.find('.show-more-terms');
            
            if (!showMoreBtn.length) return;
            
            const visibleItems = group.find('.filter-item:not(.hidden-by-filter):not(.visible)').length;
            const target = showMoreBtn.data('target');
            
            if (visibleItems === 0) {
                // Если скрытых доступных вариантов нет - скрываем кнопку
                showMoreBtn.hide();
            } else {
                // Обновляем текст
                showMoreBtn.show();
                showMoreBtn.find('.show-more-text').text('Показать еще ' + visibleItems);
                
                // Проверяем, нужно ли показывать скрытые термины
                const hiddenVisibleItems = group.find('.hidden-term.visible:not(.hidden-by-filter)').length;
                if (hiddenVisibleItems > 0) {
                    showMoreBtn.addClass('active');
                    showMoreBtn.find('.show-more-text').hide();
                    showMoreBtn.find('.show-less-text').show();
                } else {
                    showMoreBtn.removeClass('active');
                    showMoreBtn.find('.show-more-text').show();
                    showMoreBtn.find('.show-less-text').hide();
                }
            }
        });
    }
    
    // ===== ИНИЦИАЛИЗАЦИЯ КОМПАКТНЫХ ФИЛЬТРОВ =====
    function initCompactFiltersGrid() {
        // Переключение групп
        $(document).on('click', '.filter-group-toggle', function(e) {
            e.stopPropagation();
            const target = $(this).data('target');
            const group = $(this).closest('.compact-filter-group');
            const content = $('#' + target);
            
            group.toggleClass('active');
            content.slideToggle(200);
        });
        
        // Выбор/сброс всей группы
        $(document).on('click', '.group-action-select-all', function(e) {
            e.stopPropagation();
            const group = $(this).closest('.compact-filter-group');
            selectAllInGroup(group);
        });
        
        $(document).on('click', '.group-action-reset', function(e) {
            e.stopPropagation();
            const group = $(this).closest('.compact-filter-group');
            resetGroup(group);
        });
        
        // Выбор элемента
        $(document).on('click', '.filter-grid-item', function(e) {
            if ($(this).hasClass('unavailable')) return;
            
            const checkbox = $(this).find('.filter-checkbox');
            const isChecked = checkbox.is(':checked');
            
            checkbox.prop('checked', !isChecked);
            $(this).toggleClass('selected', !isChecked);
            
            updateActiveFilterTags();
        });
        
        // Поиск в фильтрах
        $(document).on('input', '.filter-search', function() {
            const searchText = $(this).val().toLowerCase();
            const attribute = $(this).data('attribute');
            const group = $(this).closest('.compact-filter-group');
            const grid = group.find('.filter-grid');
            const filteredCount = group.find('.filtered-count');
            
            let visibleCount = 0;
            
            grid.find('.filter-grid-item').each(function() {
                const termName = $(this).data('term-name').toLowerCase();
                const matches = termName.includes(searchText);
                
                if (matches) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });
            
            // Показываем счетчик отфильтрованных
            if (searchText.length > 0) {
                filteredCount.text('Найдено: ' + visibleCount).show();
            } else {
                filteredCount.hide();
            }
        });
        
        // Кнопка "Показать ещё"
        $(document).on('click', '.show-more-filters', function(e) {
            e.preventDefault();
            const target = $(this).data('target');
            const grid = $('#grid-' + target);
            const isExpanded = grid.hasClass('show-all');
            
            if (!isExpanded) {
                grid.addClass('show-all');
                $(this).find('.show-more-text').hide();
                $(this).find('.show-less-text').show();
            } else {
                grid.removeClass('show-all');
                $(this).find('.show-more-text').show();
                $(this).find('.show-less-text').hide();
            }
        });
        
        // Сохраняем оригинальные данные
        saveOriginalFilterData();
    }
    
    // ===== ВЫБОР ВСЕХ В ГРУППЕ =====
    function selectAllInGroup(group) {
        const availableItems = group.find('.filter-grid-item:not(.unavailable)');
        const checkboxes = availableItems.find('.filter-checkbox');
        
        // Если уже все выбраны - снимаем выбор, иначе выбираем все
        const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
        
        if (allChecked) {
            checkboxes.prop('checked', false);
            availableItems.removeClass('selected');
        } else {
            checkboxes.prop('checked', true);
            availableItems.addClass('selected');
        }
        
        updateActiveFilterTags();
    }
    
    // ===== СБРОС ГРУППЫ =====
    function resetGroup(group) {
        group.find('.filter-checkbox').prop('checked', false);
        group.find('.filter-grid-item').removeClass('selected');
        
        // Сбрасываем поиск если есть
        group.find('.filter-search').val('').trigger('input');
        
        updateActiveFilterTags();
    }
    
    // ===== СОХРАНЕНИЕ ОРИГИНАЛЬНЫХ ДАННЫХ =====
    function saveOriginalFilterData() {
        $('.filter-grid-item').each(function() {
            const item = $(this);
            const count = item.find('.filter-item-count').text();
            
            item.data({
                'original-count': count,
                'original-display': item.css('display')
            });
        });
    }
    
    // ===== ОБНОВЛЕНИЕ ВИДИМОСТИ ФИЛЬТРОВ ПОСЛЕ ВЫБОРА =====
    function updateFilterVisibility(countsData, activeFilters) {
        // Для каждой группы фильтров
        $('.compact-filter-group').each(function() {
            const group = $(this);
            const attribute = group.data('attribute');
            const taxonomy = 'pa_' + attribute;
            
            if (!countsData[taxonomy]) return;
            
            const availableTerms = countsData[taxonomy];
            let hasAvailableItems = false;
            
            // Обновляем каждый элемент
            group.find('.filter-grid-item').each(function() {
                const item = $(this);
                const termSlug = item.data('term-slug');
                const checkbox = item.find('.filter-checkbox');
                const isSelected = checkbox.is(':checked');
                const isAvailable = availableTerms.hasOwnProperty(termSlug);
                const count = isAvailable ? availableTerms[termSlug] : 0;
                
                // Обновляем счетчик
                item.find('.filter-item-count').text(count);
                
                // Обновляем состояние
                if (count === 0) {
                    item.addClass('zero-items');
                } else {
                    item.removeClass('zero-items');
                }
                
                if (!isAvailable && !isSelected) {
                    // Недоступный и не выбранный
                    item.addClass('unavailable').removeClass('selected');
                    checkbox.prop('disabled', true);
                } else if (!isAvailable && isSelected) {
                    // Недоступный но выбранный
                    item.addClass('unavailable zero-items');
                    checkbox.prop('disabled', false);
                } else {
                    // Доступный
                    item.removeClass('unavailable');
                    checkbox.prop('disabled', false);
                    hasAvailableItems = true;
                }
            });
            
            // Управляем видимостью группы
            if (!hasAvailableItems) {
                group.closest('.filter-group-wrapper').slideUp(300);
            } else {
                group.closest('.filter-group-wrapper').slideDown(300);
            }
        });
    }
    
    // ===== ПОЛНЫЙ СБРОС ФИЛЬТРОВ =====
    function resetAllFilters() {
        console.log('Resetting all filters...');
        
        // 1. Сбрасываем все чекбоксы
        $('.filter-checkbox').prop('checked', false);
        $('.filter-grid-item').removeClass('selected unavailable zero-items');
        
        // 2. Восстанавливаем оригинальные счетчики
        $('.filter-grid-item').each(function() {
            const item = $(this);
            const originalCount = item.data('original-count');
            
            if (originalCount !== undefined) {
                item.find('.filter-item-count').text(originalCount);
            }
            
            item.find('.filter-checkbox').prop('disabled', false);
        });
        
        // 3. Сбрасываем поиск
        $('.filter-search').val('').trigger('input');
        
        // 4. Закрываем расширенные списки
        $('.filter-grid').removeClass('show-all');
        $('.show-more-filters').each(function() {
            $(this).find('.show-more-text').show();
            $(this).find('.show-less-text').hide();
        });
        
        // 5. Показываем все группы
        $('.filter-group-wrapper').slideDown(300);
        
        // 6. Обновляем теги
        updateActiveFilterTags();
        
        // 7. Применяем фильтрацию
        const categoryId = $('.products-grid').data('category-id');
        if (categoryId) {
            applySmartFilters(categoryId, 1);
        }
    }


    // Добавьте этот код в main.js

    // Load More functionality
    document.addEventListener('DOMContentLoaded', function() {
        const loadMoreBtn = document.getElementById('load-more-news');
        
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const button = this;
                const page = parseInt(button.getAttribute('data-page'));
                const maxPages = parseInt(button.getAttribute('data-max-pages'));
                const newsGrid = document.getElementById('news-grid');
                const spinner = document.querySelector('.loading-spinner');
                
                // Показываем спиннер
                button.style.display = 'none';
                if (spinner) {
                    spinner.style.display = 'block';
                }
                
                // AJAX запрос
                fetch(severcon_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'load_more_news',
                        nonce: severcon_ajax.nonce,
                        page: page + 1,
                        category: button.getAttribute('data-category') || '',
                        tag: button.getAttribute('data-tag') || ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Добавляем новые посты
                        newsGrid.innerHTML += data.data.html;
                        
                        // Обновляем номер страницы
                        button.setAttribute('data-page', data.data.current_page);
                        
                        // Проверяем, есть ли еще страницы
                        if (data.data.current_page >= data.data.max_pages) {
                            button.style.display = 'none';
                        } else {
                            button.style.display = 'block';
                            button.textContent = severcon_ajax.load_more_text;
                        }
                    } else {
                        button.textContent = 'Нет больше новостей';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.textContent = 'Произошла ошибка';
                })
                .finally(() => {
                    // Скрываем спиннер
                    if (spinner) {
                        spinner.style.display = 'none';
                    }
                });
            });
        }
    });
    
    
    // ===== ОСНОВНАЯ ИНИЦИАЛИЗАЦИЯ =====
    function initAll() {
        console.log('Initializing all functions...');
        
        handleStickyHeader();
        initMobileMenu();
        initSearch();
        initRequestForm();
        initEscapeHandler();
        initScrollHandlers();
        initVerticalSlider();
        initProductGallery();
        initProductRequestButtons();
        initQuickView();
        initQuickViewButtons();
        initQuickViewGallery();
        initCompactFiltersGrid();
        initViewAllButton();
        
        console.log('All functions initialized');
    }
    
    // ЗАПУСК
    initAll();
});
