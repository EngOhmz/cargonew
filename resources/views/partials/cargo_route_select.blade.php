<div class="cargo-route-field form-group {{ $class ?? 'mb-0' }}">
    <div class="cargo-route-drop-grid">
        <div class="cargo-route-drop-block">
            <label class="cargo-route-label">
                Njia ya Usafirishaji
                <small class="cargo-route-label-hint">Route kuu</small>
            </label>
            <div class="cargo-route-select-wrap">
                <select name="{{ $routeName ?? 'route_id[]' }}"
                    class="form-control form-control-lg route-select cargo-main-route {{ $selectClass ?? '' }}"
                    @if(!empty($required)) required @endif
                    @if(!empty($disabled)) disabled @endif>
                    <option value="">Chagua route - mfano: Dar es Salaam → Dodoma</option>
                    @foreach($cargo_routes as $route)
                        <option value="{{ $route->id }}"
                            data-from="{{ $route->fromMikoa->regional_name ?? '' }}"
                            data-to="{{ $route->toMikoa->regional_name ?? '' }}"
                            @if(isset($selectedRoute) && (string) $selectedRoute === (string) $route->id) selected @endif>
                            {{ $route->label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
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
            <small class="form-text text-muted cargo-drop-hint">
                Mfano: idadi 8 → Morogoro 5, Iringa 3. Jumla lazima iwe sawa na idadi ya mzigo.
            </small>
        </div>
        @endif
    </div>
</div>
