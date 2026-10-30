/*
 * sh-select2-modal-fix.js
 *
 * Global fix for Select2 inside Bootstrap 5 modals.
 *
 * Problem: When a Select2 control is inside a .modal, its dropdown (which
 * contains the search <input>) is appended to <body> by default — outside
 * the modal. Bootstrap 5's modal focus trap then reclaims focus on every
 * keystroke, so the Select2 search input never receives typed characters.
 *
 * Fix: Wrap $.fn.select2 once. If the target element has a .modal ancestor
 * and the caller didn't already pass a `dropdownParent`, inject it.
 *
 * Backward compatible:
 *  - Calls outside a modal: unchanged.
 *  - Calls already passing `dropdownParent`: unchanged.
 *  - Command-form calls like .select2('val', x) or .select2('destroy'):
 *    unchanged (only object-options init triggers the injection).
 *
 * Load order: after select2.full.min.js, once globally.
 */
(function ($) {
    if (!$ || !$.fn || !$.fn.select2) { return; }
    if ($.fn.select2.__shModalPatched) { return; }

    var _orig = $.fn.select2;

    function shSelect2(options) {
        var isInit = (options === undefined || options === null || $.isPlainObject(options));
        if (!isInit || !this.length) {
            return _orig.apply(this, arguments);
        }

        if (this.length === 1) {
            var $modal = this.closest('.modal');
            if ($modal.length && (!options || typeof options.dropdownParent === 'undefined')) {
                options = $.extend({}, options || {}, { dropdownParent: $modal });
            }
            return _orig.call(this, options);
        }

        // Bulk init across N elements: closest('.modal') on the whole jQuery
        // collection collapses to a deduplicated set of modal ancestors and
        // Select2 then gets a multi-element dropdownParent — which breaks
        // dropdown positioning when the page has more than one modal.
        // Init each element separately so each instance gets its OWN modal.
        var $set = this;
        $set.each(function () {
            var $el = $(this);
            var $modal = $el.closest('.modal');
            var opts = $.extend({}, options || {});
            if ($modal.length && typeof opts.dropdownParent === 'undefined') {
                opts.dropdownParent = $modal;
            }
            _orig.call($el, opts);
        });
        return $set;
    }

    $.extend(shSelect2, _orig);
    shSelect2.__shModalPatched = true;
    $.fn.select2 = shSelect2;

    // Force-close the dropdown when an option is selected.
    // Patches a Select2 4.0.0 + Bootstrap 5 modal race where CloseOnSelect's
    // 'close' trigger doesn't fully tear down the dropdown DOM.
    // Scoped to non-multi selects — multi-selects intentionally keep the
    // dropdown open after each pick to allow further selections.
    $(document).on('select2:select.shForceClose', 'select.select2-hidden-accessible:not([multiple])', function () {
        var inst = $(this).data('select2');
        if (inst && inst.isOpen()) {
            $(this).select2('close');
        }
    });

    // Close every OTHER open Select2 when a new one opens.
    // Fixes "two dropdowns visible simultaneously" inside modals where
    // multiple instances share the same dropdownParent.
    $(document).on('select2:opening.shCloseOthers', 'select.select2-hidden-accessible', function (e) {
        var $current = $(e.target);
        $('select.select2-hidden-accessible').not($current).each(function () {
            var inst = $(this).data('select2');
            if (inst && inst.isOpen()) {
                $(this).select2('close');
            }
        });
    });
}(jQuery));
