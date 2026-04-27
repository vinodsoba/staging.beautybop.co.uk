define(["Magento_Ui/js/lib/view/utils/async"], function ($) {
  "use strict";

  var expandables = [".discount-code", ".order-attachments"];

  $.async(
    {
      selector: expandables.join(","),
      ctx: $(".checkout-container").get(0),
    },
    function (el) {
      setTimeout(function () {
        $(el).collapsible("activate");
      }, 500);
    }
  );
});
