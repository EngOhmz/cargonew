<script type="application/json" id="cargo-route-stops-json">@json($cargo_route_stops ?? new \stdClass())</script>
<script>window.allowEnrouteDrops = @json((bool) config('app.allow_enroute_drops'));</script>

<script type="text/template" id="cargo-route-options-html">
    <option value="">Chagua route - mfano: Dar es Salaam → Dodoma</option>
    @foreach($cargo_routes as $route)
        <option value="{{ $route->id }}"
            data-from="{{ $route->fromMikoa->regional_name ?? '' }}"
            data-to="{{ $route->toMikoa->regional_name ?? '' }}">{{ $route->label }}</option>
    @endforeach
</script>

<script type="text/template" id="cargo-route-split-wrap-html">
    @if(config('app.allow_enroute_drops'))
    <div class="cargo-route-drop-block cargo-route-drop-block--highlight cargo-split-destinations-wrap" style="display:none;">
        <label class="cargo-route-label">
            Mgawanyo wa Idadi
            <small class="cargo-route-label-hint">Gawa idadi kwa kila kituo</small>
        </label>
        <div class="cargo-split-rows"></div>
        <div class="cargo-split-total mt-2">
            <small class="text-muted">
                Jumla: <strong class="cargo-split-sum">0</strong> / <strong class="cargo-split-target">0</strong>
                <span class="cargo-split-status ml-2"></span>
            </small>
        </div>
        <small class="form-text text-danger cargo-split-error d-none"></small>
        <small class="form-text text-muted cargo-drop-hint">
            Mfano: idadi 8 → Morogoro 5, Iringa 3. Futa uandike upya - sehemu tupu = 0.
        </small>
    </div>
    @endif
</script>

<script>
(function() {
    var routeStopsMap = {};

    function loadRouteStopsMap() {
        try {
            var el = document.getElementById('cargo-route-stops-json');
            if (!el) return {};
            var parsed = JSON.parse(el.textContent || '{}');
            return parsed && typeof parsed === 'object' ? parsed : {};
        } catch (e) {
            console.error('cargo-route-stops-json parse error', e);
            return {};
        }
    }

    routeStopsMap = loadRouteStopsMap();

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function getPackageIndex($field) {
        var $item = $field.closest('.package-item');
        var idx = $item.data('package-index');
        if (idx === undefined || idx === null || idx === '') {
            idx = $item.index();
        }
        return parseInt(idx, 10) || 0;
    }

    function getPackageQuantity($field) {
        var qty = parseInt($field.closest('.package-item').find('.item_quantity').val(), 10);
        return isNaN(qty) ? 0 : qty;
    }

    function reindexPackageSplits($container) {
        if (!$container || !$container.length) {
            return;
        }

        $container.find('.package-item').each(function(i) {
            var $item = $(this);
            $item.attr('data-package-index', i);
            $item.find('.cargo-split-qty').each(function() {
                var $input = $(this);
                var mikoaId = $input.data('mikoa-id');
                if (mikoaId) {
                    $input.attr('name', 'drop_split[' + i + '][' + mikoaId + ']');
                }
            });
        });
    }

    function parseSplitQty(raw) {
        var text = String(raw == null ? '' : raw).trim();
        if (text === '') {
            return null;
        }
        var n = parseInt(text, 10);
        if (isNaN(n) || n < 0) {
            return null;
        }
        return n;
    }

    function clampSplitInput($input, options) {
        options = options || {};
        var allowEmpty = options.allowEmpty !== false;
        var $wrap = $input.closest('.cargo-split-destinations-wrap');
        var target = parseInt($wrap.find('.cargo-split-target').text(), 10) || 0;
        if (target <= 0) {
            return;
        }

        var raw = $input.val();
        var current = parseSplitQty(raw);

        if (current === null) {
            if (!allowEmpty) {
                $input.val('');
            }
            return;
        }

        var others = 0;
        $wrap.find('.cargo-split-qty').each(function() {
            if (this !== $input[0]) {
                var v = parseSplitQty($(this).val());
                if (v !== null && v > 0) {
                    others += v;
                }
            }
        });

        var maxAllowed = Math.max(0, target - others);
        if (current > maxAllowed) {
            $input.val(maxAllowed);
        }
    }

    function normalizeSplitInput($input) {
        var v = parseSplitQty($input.val());
        if (v === null) {
            $input.val('');
        } else {
            $input.val(v);
        }
    }

    function updateSplitTotal($wrap) {
        var sum = 0;
        $wrap.find('.cargo-split-qty').each(function() {
            var v = parseSplitQty($(this).val());
            if (v !== null) {
                sum += v;
            }
        });

        var target = parseInt($wrap.find('.cargo-split-target').text(), 10) || 0;
        $wrap.find('.cargo-split-sum').text(sum);

        var $status = $wrap.find('.cargo-split-status');
        var $error = $wrap.find('.cargo-split-error');

        $wrap.removeClass('cargo-split-invalid cargo-split-valid');

        if (target <= 0) {
            $status.text('').css('color', '');
            $error.addClass('d-none').text('');
            return;
        }

        if (sum === target) {
            $status.text('✓').css('color', '#16a34a');
            $error.addClass('d-none').text('');
            $wrap.addClass('cargo-split-valid');
        } else if (sum > target) {
            $status.text('Imezidi').css('color', '#dc2626');
            $error.removeClass('d-none').text('Jumla ya mgawanyo (' + sum + ') imezidi idadi ya mzigo (' + target + '). Punguza idadi.');
            $wrap.addClass('cargo-split-invalid');
        } else if (sum > 0) {
            $status.text('Chini').css('color', '#d97706');
            $error.removeClass('d-none').text('Jumla ya mgawanyo (' + sum + ') ni chini ya idadi ya mzigo (' + target + '). Gawa idadi zote.');
            $wrap.addClass('cargo-split-invalid');
        } else {
            $status.text('').css('color', '');
            $error.removeClass('d-none').text('Gawa idadi kwa angalau kituo kimoja.');
            $wrap.addClass('cargo-split-invalid');
        }
    }

    window.validatePackageSplit = function($item) {
        var $field = $item.find('.cargo-route-field');
        if (!$field.length) {
            return { ok: true, message: '' };
        }

        var $route = $field.find('.cargo-main-route');
        var $wrap = $field.find('.cargo-split-destinations-wrap');
        var routeId = $route.val();
        var target = getPackageQuantity($field);
        var pkgLabel = $item.find('.package-item-title').text() || ('Mzigo #' + (getPackageIndex($field) + 1));

        if (!routeId) {
            return { ok: false, message: pkgLabel + ': chagua njia ya usafirishaji.' };
        }

        if (target <= 0) {
            return { ok: false, message: pkgLabel + ': weka idadi ya mzigo kwanza.' };
        }

        if (!window.allowEnrouteDrops) {
            return { ok: true, message: '' };
        }

        if (!$wrap.is(':visible')) {
            return { ok: false, message: pkgLabel + ': hakuna vituo kwa route uliyochagua.' };
        }

        var sum = 0;
        $wrap.find('.cargo-split-qty').each(function() {
            var v = parseSplitQty($(this).val());
            if (v !== null) {
                sum += v;
            }
        });

        if (sum > target) {
            return {
                ok: false,
                message: pkgLabel + ': jumla ya mgawanyo (' + sum + ') imezidi idadi ya mzigo (' + target + ').'
            };
        }

        if (sum < target) {
            return {
                ok: false,
                message: pkgLabel + ': jumla ya mgawanyo (' + sum + ') lazima iwe sawa na idadi ya mzigo (' + target + ').'
            };
        }

        return { ok: true, message: '' };
    };

    window.validateCargoSplitForm = function($form) {
        var firstError = null;
        var $items = $form.find('.package-item:not(.readonly-item)').filter(function() {
            return $(this).find('.cargo-main-route').length > 0;
        });

        if ($items.length === 0) {
            if ($form.find('input[name="c_id"]').length) {
                return {
                    ok: false,
                    message: 'Ongeza mzigo mpya kwanza (bonyeza Ongeza Mzigo), kisha hifadhi.'
                };
            }
            return { ok: true, message: '' };
        }

        $items.each(function() {
            var result = window.validatePackageSplit($(this));
            if (!result.ok && !firstError) {
                firstError = result.message;
            }
        });

        if (firstError) {
            return { ok: false, message: firstError };
        }

        return { ok: true, message: '' };
    };

    window.formatCargoRouteOption = function(state) {
        if (!state.id) {
            return '<span class="cargo-route-placeholder">' + escapeHtml(state.text) + '</span>';
        }

        var $el = $(state.element);
        var from = $el.data('from') || '';
        var to = $el.data('to') || '';

        return (
            '<span class="cargo-route-pill">' +
                '<span class="cargo-route-from" title="Kutoka">' + escapeHtml(from) + '</span>' +
                '<span class="cargo-route-arrow" aria-hidden="true"><i class="icon-arrow-right13"></i></span>' +
                '<span class="cargo-route-to" title="Mwisho wa route">' + escapeHtml(to) + '</span>' +
            '</span>'
        );
    };

    window.populateSplitDestinations = function($field, routeId, preserveValues) {
        if (!window.allowEnrouteDrops) {
            $field.find('.cargo-split-destinations-wrap').hide();
            return;
        }
        routeStopsMap = loadRouteStopsMap();
        var $wrap = $field.find('.cargo-split-destinations-wrap');
        var $rows = $wrap.find('.cargo-split-rows');
        var pkgIndex = getPackageIndex($field);
        var targetQty = getPackageQuantity($field);
        var existing = {};

        if (preserveValues) {
            $wrap.find('.cargo-split-qty').each(function() {
                var mikoaId = $(this).data('mikoa-id');
                if (mikoaId) {
                    existing[mikoaId] = $(this).val();
                }
            });
        }

        $rows.empty();
        $wrap.find('.cargo-split-target').text(targetQty);

        var stops = routeStopsMap[routeId] || routeStopsMap[String(routeId)] || [];

        if (!routeId || !stops.length) {
            $wrap.hide();
            return;
        }

        $wrap.show();

        stops.forEach(function(stop) {
            var label = stop.name + (stop.is_final ? ' (mwisho)' : ' (njiani)');
            var prev = existing[stop.mikoa_id];
            var value = '';
            if (prev !== undefined && prev !== null && String(prev).trim() !== '') {
                value = prev;
            }
            var row = $('<div class="cargo-split-row form-row align-items-center mb-2"></div>');
            row.append(
                '<div class="col-7"><span class="cargo-split-label">' + escapeHtml(label) + '</span></div>' +
                '<div class="col-5">' +
                    '<input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="off" ' +
                    'class="form-control form-control-sm cargo-split-qty" ' +
                    'data-mikoa-id="' + stop.mikoa_id + '" ' +
                    'name="drop_split[' + pkgIndex + '][' + stop.mikoa_id + ']" ' +
                    'placeholder="0" value="' + escapeHtml(String(value)) + '" />' +
                '</div>'
            );
            $rows.append(row);
        });

        if (!preserveValues && stops.length === 1 && targetQty > 0) {
            $rows.find('.cargo-split-qty').first().val(targetQty);
        }

        $rows.find('.cargo-split-qty').each(function() {
            clampSplitInput($(this), { allowEmpty: true });
        });

        updateSplitTotal($wrap);
    };

    function refreshSplitTarget($field) {
        var $wrap = $field.find('.cargo-split-destinations-wrap');
        var targetQty = getPackageQuantity($field);
        $wrap.find('.cargo-split-target').text(targetQty);

        $wrap.find('.cargo-split-qty').each(function() {
            clampSplitInput($(this), { allowEmpty: true });
        });

        updateSplitTotal($wrap);
    }

    function onRouteChanged($route) {
        var $field = $route.closest('.cargo-route-field');
        window.populateSplitDestinations($field, $route.val(), false);
    }

    window.bindCargoRouteDropFields = function($scope) {
        $scope = $scope || $(document);
        $scope.find('.cargo-route-field').each(function() {
            var $field = $(this);
            var $route = $field.find('.cargo-main-route');
            if ($route.val()) {
                window.populateSplitDestinations($field, $route.val(), true);
            }
        });
    };

    window.initCargoRouteSelect = function($scope) {
        $scope = $scope || $(document);

        $scope.find('select.route-select').each(function() {
            var $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.off('change.cargoRoute select2:select.cargoRoute');
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                placeholder: 'Chagua route - mfano: Dar es Salaam → Dodoma',
                allowClear: true,
                dropdownCssClass: 'cargo-route-dropdown cargo-route-dropdown-lg',
                containerCssClass: 'cargo-route-select2',
                dropdownAutoWidth: false,
                templateResult: window.formatCargoRouteOption,
                templateSelection: window.formatCargoRouteOption,
                escapeMarkup: function(markup) { return markup; },
                language: {
                    noResults: function() { return 'Hakuna route inayolingana'; },
                    searching: function() { return 'Inatafuta...'; }
                }
            });

            $select.on('change.cargoRoute select2:select.cargoRoute', function() {
                onRouteChanged($(this));
            });
        });

        window.bindCargoRouteDropFields($scope);

        // When init is called on a single .package-item, still reindex the whole container
        var $container = $scope.closest('#package_container, #new_package_container');
        if (!$container.length) {
            $container = $scope.find('#package_container, #new_package_container');
        }
        if (!$container.length) {
            $container = $('#package_container, #new_package_container');
        }
        reindexPackageSplits($container);
    };

    $(document).on('change.cargoRoute select2:select.cargoRoute', '.cargo-main-route', function() {
        onRouteChanged($(this));
    });

    $(document).on('input change', '.item_quantity', function() {
        var $item = $(this).closest('.package-item');
        var $field = $item.find('.cargo-route-field');
        var $route = $field.find('.cargo-main-route');
        if ($route.val() && $field.find('.cargo-split-rows .cargo-split-qty').length) {
            refreshSplitTarget($field);
        } else if ($route.val()) {
            window.populateSplitDestinations($field, $route.val(), false);
        }
    });

    $(document).on('input', '.cargo-split-qty', function() {
        var $input = $(this);
        var cleaned = String($input.val()).replace(/[^\d]/g, '');
        if ($input.val() !== cleaned) {
            $input.val(cleaned);
        }
        clampSplitInput($input, { allowEmpty: true });
        updateSplitTotal($input.closest('.cargo-split-destinations-wrap'));
    });

    $(document).on('blur', '.cargo-split-qty', function() {
        var $input = $(this);
        normalizeSplitInput($input);
        clampSplitInput($input, { allowEmpty: true });
        updateSplitTotal($input.closest('.cargo-split-destinations-wrap'));
    });

    $(document).on('submit', '.registration-form form', function(e) {
        var $form = $(this);
        var result = window.validateCargoSplitForm($form);

        if (!result.ok) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var $alert = $form.find('.cargo-split-form-alert').first();
            if (!$alert.length) {
                $alert = $('<div class="alert alert-danger cargo-split-form-alert"></div>');
                $form.prepend($alert);
            }

            $alert.removeClass('d-none').text(result.message).show();

            $('html, body').animate({ scrollTop: $alert.offset().top - 120 }, 300);

            var $bad = $form.find('.cargo-split-invalid').first();
            if ($bad.length) {
                $('html, body').animate({ scrollTop: $bad.offset().top - 120 }, 300);
            } else {
                var $sticky = $form.find('.editmzigo-sticky-bar');
                if ($sticky.length) {
                    $sticky.addClass('border-danger');
                    setTimeout(function() { $sticky.removeClass('border-danger'); }, 3000);
                }
            }

            $('#loader').hide();
            return false;
        }

        $form.find('.cargo-split-form-alert').addClass('d-none').hide();
    });

    $(document).ready(function() {
        window.initCargoRouteSelect($('.registration-form'));
    });

    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
        if ($(e.target).attr('href') === '#home2') {
            window.initCargoRouteSelect($('#home2 .registration-form'));
        }
    });

    window.reindexPackageSplits = reindexPackageSplits;
})();
</script>
