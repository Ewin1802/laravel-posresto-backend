/* ARCH Manajemen — UI behaviour (needs jQuery + Bootstrap 4 JS) */
(function ($) {
    'use strict';

    var idFormat = new Intl.NumberFormat('id-ID');

    window.ARCH = {
        rupiah: function (n, decimals) {
            var v = Number(n || 0);
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: decimals || 0,
                maximumFractionDigits: decimals || 0
            }).format(v);
        },
        number: function (n) { return idFormat.format(Number(n || 0)); }
    };

    $(function () {
        var $body = $('body');

        /* Sidebar (mobile drawer) */
        $(document).on('click', '[data-sidebar-toggle]', function (e) {
            e.preventDefault();
            $body.toggleClass('sidebar-open');
        });
        $(document).on('click', '.sidebar-backdrop', function () { $body.removeClass('sidebar-open'); });
        $(document).on('keydown', function (e) { if (e.key === 'Escape') $body.removeClass('sidebar-open'); });

        /* Sidebar ciut / lebar (desktop), diingat per perangkat */
        var root = document.documentElement;
        var desktop = window.matchMedia('(min-width: 992px)');
        var syncCollapseUi = function () {
            var collapsed = root.classList.contains('sb-collapsed');
            $('[data-sidebar-collapse]')
                .attr('aria-expanded', collapsed ? 'false' : 'true')
                .attr('aria-label', collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar');
        };
        var toggleCollapse = function () {
            var collapsed = root.classList.toggle('sb-collapsed');
            try { localStorage.setItem('arch.sidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
            hideTip();
            syncCollapseUi();
            // beri tahu grafik agar menyesuaikan lebar setelah animasi
            setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 340);
        };
        $(document).on('click', '[data-sidebar-collapse]', function (e) { e.preventDefault(); toggleCollapse(); });
        $(document).on('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && !e.shiftKey && !e.altKey && (e.key === 'b' || e.key === 'B') && desktop.matches) {
                e.preventDefault(); toggleCollapse();
            }
        });
        syncCollapseUi();
        requestAnimationFrame(function () { requestAnimationFrame(function () { root.classList.remove('sb-preload'); }); });

        /* Tooltip label saat sidebar ciut */
        var $tip = $('<div class="sb-tip" role="tooltip"></div>').appendTo('body');
        var hideTip = function () { $tip.removeClass('is-on'); };
        $(document).on('mouseenter focusin', '.sidebar [data-label]', function () {
            if (!root.classList.contains('sb-collapsed') || !desktop.matches) return;
            var r = this.getBoundingClientRect();
            $tip.text($(this).data('label')).css({ left: r.right + 12, top: r.top + r.height / 2 - 16 }).addClass('is-on');
        });
        $(document).on('mouseleave focusout', '.sidebar [data-label]', hideTip);
        $('.sidebar__nav').on('scroll', hideTip);

        /* Topbar hairline once content scrolls under it */
        var $topbar = $('.topbar');
        var onScroll = function () { $topbar.toggleClass('is-scrolled', window.scrollY > 4); };
        $(window).on('scroll', onScroll); onScroll();

        /* Flash messages */
        $(document).on('click', '.flash__close', function () {
            $(this).closest('.flash').slideUp(150, function () { $(this).remove(); });
        });

        /* Confirm dialog for destructive forms: <form data-confirm="..."> */
        var $confirm = $('#confirmModal');
        var pendingForm = null;
        $(document).on('submit', 'form[data-confirm]', function (e) {
            var form = this;
            if (form.dataset.confirmed === '1') return true;
            e.preventDefault();
            if (!$confirm.length) {
                if (window.confirm(form.dataset.confirm)) { form.dataset.confirmed = '1'; form.submit(); }
                return false;
            }
            pendingForm = form;
            $confirm.find('[data-confirm-title]').text(form.dataset.confirmTitle || 'Hapus data ini?');
            $confirm.find('[data-confirm-text]').text(form.dataset.confirm);
            $confirm.find('[data-confirm-ok]').text(form.dataset.confirmOk || 'Hapus');
            $confirm.modal('show');
            return false;
        });
        $confirm.on('click', '[data-confirm-ok]', function () {
            if (!pendingForm) return;
            var $btn = $(this).prop('disabled', true);
            pendingForm.dataset.confirmed = '1';
            pendingForm.submit();
            setTimeout(function () { $btn.prop('disabled', false); }, 3000);
        });
        $confirm.on('hidden.bs.modal', function () { pendingForm = null; });

        /* Rupiah inputs: <input data-rupiah="#hiddenId"> keeps a raw number in the hidden field */
        $('[data-rupiah]').each(function () {
            var $display = $(this);
            var $hidden = $($display.data('rupiah'));
            var sync = function () {
                var raw = String($display.val()).replace(/[^0-9]/g, '');
                $display.val(raw ? idFormat.format(raw) : '');
                $hidden.val(raw);
            };
            if (!$display.val() && $hidden.val()) {
                $display.val(idFormat.format(String($hidden.val()).split('.')[0]));
            }
            $display.on('input', sync);
        });

        /* File inputs with live preview */
        $('.dropzone').each(function () {
            var $zone = $(this);
            var $input = $zone.find('input[type=file]');
            var $preview = $zone.find('.dropzone__preview');
            var $name = $zone.find('[data-file-name]');
            $input.on('dragenter dragover', function () { $zone.addClass('is-drag'); })
                  .on('dragleave drop', function () { $zone.removeClass('is-drag'); })
                  .on('change', function () {
                      var file = this.files && this.files[0];
                      if (!file) return;
                      $name.text(file.name + ' · ' + Math.round(file.size / 1024) + ' KB');
                      if (/^image\//.test(file.type)) {
                          var reader = new FileReader();
                          reader.onload = function (ev) {
                              $preview.css('background-image', 'url(' + ev.target.result + ')').addClass('has-image');
                          };
                          reader.readAsDataURL(file);
                      }
                  });
        });

        /* Show / hide password */
        $(document).on('click', '[data-toggle-password]', function () {
            var $input = $(this).siblings('input');
            var show = $input.attr('type') === 'password';
            $input.attr('type', show ? 'text' : 'password');
            $(this).attr('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi')
                   .find('i').toggleClass('fa-eye fa-eye-slash');
        });

        /* Prevent double submit on regular forms */
        $(document).on('submit', 'form:not([data-confirm]):not([data-no-lock])', function (e) {
            if (e.isDefaultPrevented() || (this.method || '').toLowerCase() === 'get') return;
            var $btn = $(this).find('button[type=submit]');
            setTimeout(function () { $btn.prop('disabled', true).addClass('is-loading'); }, 0);
            setTimeout(function () { $btn.prop('disabled', false).removeClass('is-loading'); }, 8000);
        });

        /* Jam navbar (WITA, mengikuti jam perangkat) */
        var $clockTime = $('[data-clock-time]'), $clockDate = $('[data-clock-date]');
        if ($clockTime.length) {
            var tick = function () {
                var d = new Date();
                try {
                    $clockTime.text(d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Makassar' }).replace('.', ':'));
                    $clockDate.text(d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', timeZone: 'Asia/Makassar' }) + ' · WITA');
                } catch (e) {}
            };
            tick();
            setTimeout(function () { tick(); setInterval(tick, 60000); }, (60 - new Date().getSeconds()) * 1000);
        }

        /* Label pintasan ⌘ di Mac */
        if (/Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent)) { $('[data-mod]').text('⌘'); }

        /* Pencarian cepat (Ctrl/⌘ + K atau "/") */
        var $cmdk = $('#cmdk');
        if ($cmdk.length) {
            var $input = $('#cmdkInput');
            var $items = $cmdk.find('[data-cmdk-item]');
            var $groups = $cmdk.find('[data-cmdk-group]');
            var $empty = $cmdk.find('.cmdk__empty');
            var lastFocus = null;
            var visible = function () { return $items.filter(function () { return !this.hidden; }); };
            var setActive = function ($el) {
                $items.removeClass('is-active').attr('aria-selected', 'false');
                if ($el && $el.length) {
                    $el.addClass('is-active').attr('aria-selected', 'true');
                    $el[0].scrollIntoView({ block: 'nearest' });
                }
            };
            var filter = function () {
                var q = $.trim($input.val()).toLowerCase();
                var words = q.split(/\s+/).filter(Boolean);
                $items.each(function () {
                    var hay = this.getAttribute('data-search');
                    this.hidden = !words.every(function (w) { return hay.indexOf(w) !== -1; });
                });
                $groups.each(function () { this.hidden = !$(this).find('[data-cmdk-item]').filter(function () { return !this.hidden; }).length; });
                var $v = visible();
                $empty.prop('hidden', $v.length > 0);
                setActive($v.first());
            };
            var open = function () {
                if (!$cmdk.prop('hidden')) return;
                lastFocus = document.activeElement;
                if ($.fn.dropdown) { $('[data-toggle="dropdown"][aria-expanded="true"]').dropdown('hide'); }
                $cmdk.prop('hidden', false);
                $('body').css('overflow', 'hidden');
                $input.val(''); filter();
                setTimeout(function () { $input.trigger('focus'); }, 10);
            };
            var close = function () {
                if ($cmdk.prop('hidden')) return;
                $cmdk.prop('hidden', true);
                $('body').css('overflow', '');
                if (lastFocus && lastFocus.focus) lastFocus.focus();
            };
            $(document).on('click', '[data-cmdk-open]', function (e) { e.preventDefault(); open(); });
            $cmdk.on('click', '[data-cmdk-close]', close);
            $input.on('input', filter);
            $cmdk.on('mousemove', '[data-cmdk-item]', function () { if (!$(this).hasClass('is-active')) setActive($(this)); });
            $cmdk.on('keydown', function (e) {
                var $v = visible(), idx = $v.index($v.filter('.is-active'));
                if (e.key === 'ArrowDown') { e.preventDefault(); setActive($v.eq((idx + 1) % $v.length)); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); setActive($v.eq((idx - 1 + $v.length) % $v.length)); }
                else if (e.key === 'Enter') {
                    var $a = $v.filter('.is-active');
                    if ($a.length) { e.preventDefault(); $a[0].click(); close(); }
                }
                else if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); close(); }
                else if (e.key === 'Tab') { e.preventDefault(); $input.trigger('focus'); }
            });
            $(document).on('keydown', function (e) {
                var typing = /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) || e.target.isContentEditable;
                if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) { e.preventDefault(); $cmdk.prop('hidden') ? open() : close(); }
                else if (e.key === '/' && !typing && $cmdk.prop('hidden')) { e.preventDefault(); open(); }
            });
        }

        /* Panduan (panel samping): [data-guide-open="nama"] membuka #guide-nama */
        var guideLast = null;
        var openGuide = function (name) {
            var $g = $('#guide-' + name);
            if (!$g.length) return;
            guideLast = document.activeElement;
            $g.prop('hidden', false);
            $('body').css('overflow', 'hidden');
            requestAnimationFrame(function () { $g.addClass('is-open'); $g.find('.guide__close').trigger('focus'); });
        };
        var closeGuide = function ($g) {
            if (!$g.length || $g.prop('hidden')) return;
            $g.removeClass('is-open');
            $('body').css('overflow', '');
            setTimeout(function () { $g.prop('hidden', true); }, 380);
            if (guideLast && guideLast.focus) guideLast.focus();
        };
        $(document).on('click', '[data-guide-open]', function (e) { e.preventDefault(); openGuide($(this).data('guide-open')); });
        $(document).on('click', '[data-guide-close]', function () { closeGuide($(this).closest('.guide')); });
        $(document).on('keydown', function (e) { if (e.key === 'Escape') closeGuide($('.guide.is-open')); });
        // Tampil otomatis sekali per perangkat
        $('[data-guide-auto]').each(function () {
            var key = $(this).data('guide-auto'), name = $(this).data('guide-open');
            var seen = null;
            try { seen = localStorage.getItem(key); localStorage.setItem(key, '1'); } catch (e) { seen = '1'; }
            if (!seen) setTimeout(function () { openGuide(name); }, 500);
        });

        /* Bootstrap tooltips if available */
        if ($.fn.tooltip) { $('[data-toggle="tooltip"]').tooltip(); }
    });
})(jQuery);
