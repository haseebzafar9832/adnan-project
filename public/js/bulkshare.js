/* BulkShare jQuery AJAX: live prices, catalogue search, group chat, notification count. */
(function ($) {
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': token }
    });

    $(document).on('click', '.password-toggle', function () {
        var button = $(this);
        var input = button.siblings('input');
        var visible = input.attr('type') === 'text';
        input.attr('type', visible ? 'password' : 'text');
        button.find('i').toggleClass('bi-eye bi-eye-slash');
        button.attr('aria-label', visible ? 'Show password' : 'Hide password');
    });

    function money(value) {
        var amount = Math.round(Number(value) || 0);
        return 'Rs ' + amount.toLocaleString('en-US');
    }

    function percent(value) {
        var number = Number(value) || 0;
        return (Math.round(number * 10) / 10).toString().replace(/\.0$/, '') + '%';
    }

    function units(unit, count) {
        if (Number(count) === 1) {
            return unit;
        }
        return unit === 'box' ? 'boxes' : unit + 's';
    }

    function fillQuote(data, personal) {
        var box = $('#quote-preview');
        if (!box.length || !data) {
            return;
        }

        box.find('[data-q="unit"]').text(money(data.unit_price) + ' / ' + data.unit);
        box.find('[data-q="line"]').text(money(data.line_total) + ' for ' + personal + ' ' + units(data.unit, personal));
        box.find('[data-q="off"]').text(data.discount_percent > 0
            ? percent(data.discount_percent) + ' off' + (data.tier_name ? ' · ' + data.tier_name + ' tier' : '')
            : 'Shelf price. The first tier is still ahead.');

        var next = box.find('[data-q="next"]');
        if (data.next_tier) {
            next.text(data.next_tier.units_needed + ' more ' + units(data.unit, data.next_tier.units_needed) + ' unlocks ' + data.next_tier.name + ' at ' + percent(data.next_tier.discount_percent) + ' (' + money(data.next_tier.unit_price) + ').');
        } else {
            next.text('This is the best published tier.');
        }
    }

    function requestQuote(productId, groupQty, personal) {
        var box = $('#quote-preview');
        return $.getJSON(box.data('url'), {
            product_id: productId,
            group_quantity: groupQty,
            personal_quantity: personal
        });
    }

    var quoteTimer;
    function refreshQuote() {
        var box = $('#quote-preview');
        if (!box.length) {
            return;
        }

        var productId = $('#product_id').val() || box.data('product');
        if (!productId) {
            return;
        }

        var mode = box.data('mode');
        window.clearTimeout(quoteTimer);
        quoteTimer = window.setTimeout(function () {
            if (mode === 'create') {
                var mine = Math.max(1, parseInt($('#my_quantity').val(), 10) || 1);
                var target = Math.max(mine, parseInt($('#target_quantity').val(), 10) || mine);
                requestQuote(productId, mine, mine).done(function (solo) {
                    box.find('[data-q="solo"]').text(money(solo.unit_price) + ' · ' + (solo.discount_percent > 0 ? percent(solo.discount_percent) + ' off' : 'shelf price'));
                });
                requestQuote(productId, target, mine).done(function (hit) {
                    box.find('[data-q="target"]').text(money(hit.unit_price) + ' · ' + (hit.discount_percent > 0 ? percent(hit.discount_percent) + ' off' : 'shelf price'));
                    box.find('[data-q="save"]').text('Your ' + mine + ' would cost ' + money(hit.line_total) + ' if the target is met.');
                });
                return;
            }

            var personal = Math.max(1, parseInt($('#quantity').val(), 10) || 1);
            var current = parseInt(box.data('current'), 10) || 0;
            requestQuote(productId, current + personal, personal).done(function (data) {
                fillQuote(data, personal);
            });
        }, 180);
    }

    $('#quantity, #my_quantity, #target_quantity, #product_id').on('input change', refreshQuote);

    var filterTimer;
    function loadProducts(url) {
        var grid = $('#product-grid');
        grid.addClass('opacity-50');
        $.get(url).done(function (html) {
            grid.html(html);
            window.history.replaceState({}, '', url);
        }).always(function () {
            grid.removeClass('opacity-50');
        });
    }

    $('#product-filter').on('submit', function (event) {
        event.preventDefault();
        loadProducts('?' + $(this).serialize());
    });

    $('#product-filter input, #product-filter select').on('input change', function () {
        window.clearTimeout(filterTimer);
        filterTimer = window.setTimeout(function () {
            $('#product-filter').trigger('submit');
        }, 250);
    });

    $(document).on('click', '#product-grid .pagination a', function (event) {
        event.preventDefault();
        loadProducts($(this).attr('href'));
    });

    $('#pay-method').on('change', function () {
        var method = $(this).val();
        $('[data-pay]').addClass('d-none');
        if (method === 'jazzcash' || method === 'easypaisa') {
            $('[data-pay="wallet"]').removeClass('d-none');
        } else {
            $('[data-pay="' + method + '"]').removeClass('d-none');
        }
    }).trigger('change');

    var panel = $('#message-panel');
    if (panel.length && panel.data('member') === 1) {
        var seen = {};
        var lastId = parseInt(panel.data('last'), 10) || 0;

        function paint(message) {
            if (seen[message.id]) {
                return;
            }
            seen[message.id] = true;
            lastId = Math.max(lastId, message.id);
            var row = $('<div class="msg"></div>').toggleClass('is-mine', !!message.mine).attr('data-id', message.id);
            var top = $('<div class="msg-top"></div>');
            top.append($('<strong></strong>').text(message.name));
            top.append($('<time></time>').text(message.time));
            row.append(top);
            row.append($('<p></p>').text(message.body));
            panel.find('.thread').append(row);
        }

        panel.find('.msg').each(function () {
            seen[$(this).data('id')] = true;
        });

        var thread = panel.find('.thread');
        thread.scrollTop(thread.prop('scrollHeight'));

        function poll() {
            $.getJSON(panel.data('poll'), { after: lastId }).done(function (payload) {
                (payload.messages || []).forEach(paint);
                if ((payload.messages || []).length) {
                    thread.scrollTop(thread.prop('scrollHeight'));
                }
            });
        }

        window.setInterval(poll, 4000);

        $('#message-form').on('submit', function (event) {
            event.preventDefault();
            var form = $(this);
            var body = $.trim(form.find('[name="body"]').val());
            if (!body) {
                return;
            }
            $.post(form.attr('action'), { body: body }).done(function (payload) {
                if (payload.message) {
                    paint(payload.message);
                    thread.scrollTop(thread.prop('scrollHeight'));
                }
                form.find('[name="body"]').val('');
            });
        });
    }

    var bell = $('#nav-alerts');
    if (bell.length) {
        window.setInterval(function () {
            $.getJSON(bell.data('count-url')).done(function (payload) {
                var count = payload.count || 0;
                var badge = bell.find('.count');
                badge.text(count);
                badge.prop('hidden', count < 1);
            });
        }, 25000);
    }
})(jQuery);
