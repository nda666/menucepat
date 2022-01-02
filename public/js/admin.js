(function () {
  var dropdownMenu;

  // and when you show it, move it to the body
  $(window).on("show.bs.dropdown", function (e) {
    // grab the menu
    dropdownMenu = $(e.target).find(".dropdown-menu")[0];

    // detach it and append it to the body
    $("body").append($(dropdownMenu).detach());

    // grab the new offset position
    var eOffset = $(e.target).offset();

    // make sure to place it where it would normally go (this could be improved)
    $(dropdownMenu).css({
      display: "block",
      top: eOffset.top + $(e.target).outerHeight(),
      left: eOffset.left,
    });
  });

  // and when you hide it, reattach the drop down, and hide it normally
  $(window).on("hide.bs.dropdown", function (e) {
    $(dropdownMenu).hide();
    $(e.target).append($(dropdownMenu).detach());
    dropdownMenu = null;
  });

  $.extend(true, $.fn.dataTable.defaults, {
    searching: false,
  });

  (function ($) {
    var Defaults = $.fn.select2.amd.require("select2/defaults");

    $.extend(Defaults.defaults, {
      searchInputPlaceholder: "",
    });

    var SearchDropdown = $.fn.select2.amd.require("select2/dropdown/search");

    var _renderSearchDropdown = SearchDropdown.prototype.render;

    SearchDropdown.prototype.render = function (decorated) {
      // invoke parent method
      var $rendered = _renderSearchDropdown.apply(
        this,
        Array.prototype.slice.apply(arguments)
      );

      this.$search.attr(
        "placeholder",
        this.options.get("searchInputPlaceholder")
      );

      return $rendered;
    };
  })(window.jQuery);
})();
