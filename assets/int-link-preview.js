jQuery(document).ready(function($) {
    let $tooltip = $('<div id="ilpt-tooltip" class="ilpt-tooltip" style="display: none; opacity: 0;"></div>').appendTo('body');
    let hoverTimer;
    let hideTimer;
    let currentUrl = '';

    let contentSelector = 'body.single main article div.entry-content a';

    $(document).on('mouseenter', contentSelector, function(e) {
        let $link = $(this);
        let url = $link.prop('href');
        
        if (!url || $link.prop('hostname') !== window.location.hostname || url.indexOf('#') !== -1) {
            return;
        }

        clearTimeout(hideTimer);

        hoverTimer = setTimeout(function() {
            currentUrl = url;
            $tooltip.html('<div class="ilpt-loading">' + ilpt_ajax.loading_text + '</div>').show().css('opacity', '1');

            let offset = $link.offset();
            let leftPosition = offset.left;

            if (leftPosition + 320 > $(window).width()) {
                leftPosition = $(window).width() - 340;
            }

            let arrowHeight = 10; 
            let spacing = 5;
            let totalOffset = arrowHeight + spacing;

            let topPosition = offset.top - totalOffset;
            let transformValue = 'translateY(-100%)';
            $tooltip.removeClass('arrow-top'); 

            // 1. Tăng mức dự đoán khoảng trống an toàn lên 300px thay vì 150px
            let estimatedHeight = 300; 
            if (offset.top - $(window).scrollTop() < estimatedHeight + totalOffset) {
                topPosition = offset.top + $link.outerHeight() + totalOffset;
                transformValue = 'translateY(0)';
                $tooltip.addClass('arrow-top'); 
            }

            $tooltip.css({
                top: topPosition + 'px',
                left: leftPosition + 'px',
                transform: transformValue
            });

            $.post(ilpt_ajax.ajax_url, {
                action: 'ilpt_get_preview',
                url: url
            }, function(response) {
                if (response.success && currentUrl === url) {
                    let html = `
                        <p class="ilpt-tooltip-title">&#10162; ${response.data.title}</p>
                        <p class="ilpt-tooltip-content">${response.data.content}</p>
                        <a href="${response.data.url}" class="ilpt-tooltip-btn" target="_blank">${ilpt_ajax.read_more}</a>
                    `;
                    $tooltip.html(html);

                    // 2. TÍNH TOÁN LẠI VỊ TRÍ SAU KHI CÓ CHIỀU CAO THỰC TẾ CỦA NỘI DUNG
                    let actualHeight = $tooltip.outerHeight();
                    
                    if (offset.top - $(window).scrollTop() < actualHeight + totalOffset) {
                        // Không đủ chỗ phía trên -> Chuyển hướng xuống dưới
                        $tooltip.css({
                            top: (offset.top + $link.outerHeight() + totalOffset) + 'px',
                            transform: 'translateY(0)'
                        }).addClass('arrow-top');
                    } else {
                        // Đủ chỗ phía trên -> Giữ nguyên ở trên
                        $tooltip.css({
                            top: (offset.top - totalOffset) + 'px',
                            transform: 'translateY(-100%)'
                        }).removeClass('arrow-top');
                    }

                } else if (!response.success && currentUrl === url) {
                    $tooltip.hide(); 
                }
            });
        }, 300);

    }).on('mouseleave', contentSelector, function() {
        clearTimeout(hoverTimer);
        hideTimer = setTimeout(function() {
            if (!$tooltip.is(':hover')) {
                $tooltip.css('opacity', '0');
                setTimeout(() => $tooltip.hide(), 200);
            }
        }, 300);
    });

    $tooltip.on('mouseenter', function() {
        clearTimeout(hideTimer);
    }).on('mouseleave', function() {
        $tooltip.css('opacity', '0');
        setTimeout(() => $(this).hide(), 200);
    });
});