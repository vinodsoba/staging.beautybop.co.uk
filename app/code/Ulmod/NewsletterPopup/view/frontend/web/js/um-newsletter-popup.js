/**
 * Copyright Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
  "jquery",
  "Magento_Ui/js/modal/modal",
  "jquery/jquery.cookie",
  "mage/mage",
  "jquery/ui",
], function ($, modal, cookie) {
  "use strict";

  $.widget("ulmod.processPopupNewsletter", {
    /**
     *
     * @private
     */
    _create: function () {
      var self = this,
        popup_newsletter_options = {
          type: "popup",
          responsive: true,
          innerScroll: true,
          title: this.options.popupTitle,
          buttons: false,
          modalClass: "um-popup-newsletter",
        };

      modal(popup_newsletter_options, this.element);

      var cookieLifetime = this.options.cookieLtData;
      var behaviorConfig = this.options.behaviorData;
      var delaySecConfig = this.options.delaySecData;
      var delayPerConfig = this.options.delayPerData;

      // show modal on delays and set cookie lifetime on modalclosed
      if (behaviorConfig == 3) {
        $(window).scroll(function () {
          var scrollTop = $(window).scrollTop(),
            docHeight = $(document).height(),
            winHeight = $(window).height(),
            scrollPercent = scrollTop / (docHeight - winHeight),
            optionScroll = delayPerConfig / 100;

          if (scrollPercent > optionScroll) {
            if (!$.cookie("umNewslPopupCookieLifetime")) {
              self._setPopupStyleCss();
              self.element.modal("openModal").on("modalclosed", function () {
                var date = new Date();
                date.setTime(date.getTime() + parseInt(cookieLifetime) * 1000);
                $.cookie("umNewslPopupCookieLifetime", true, {
                  expires: date,
                });
              });
            }
          }
        });
      } else {
        if (
          !$.cookie("umNewslPopupCookieLifetime") &&
          typeof cookieLifetime != undefined
        ) {
          var displayDelay = 1 * 1000;
          if (behaviorConfig == 1) {
            var displayDelay = 2 * 1000;
          } else if (behaviorConfig == 2) {
            var displayDelay = delaySecConfig * 1000;
          }
          setTimeout(function () {
            self._setPopupStyleCss();
            self.element.modal("openModal").on("modalclosed", function () {
              var date = new Date();
              date.setTime(date.getTime() + parseInt(cookieLifetime) * 1000);
              $.cookie("umNewslPopupCookieLifetime", true, {
                expires: date,
              });
            });
          }, displayDelay);
        }
      }

      // submit and set cookie lifetime
      this.element.find("form").submit(function () {
        if ($(this).validation("isValid")) {
          $.ajax({
            url: $(this).attr("action"),
            cache: true,
            data: $(this).serialize(),
            dataType: "json",
            type: "POST",
            showLoader: true,
          }).done(function (data) {
            self.element.find(".messages .message div").html(data.message);
            if (data.error) {
              self.element
                .find(".messages .message")
                .addClass("message-error error");
            } else {
              self.element
                .find(".messages .message")
                .addClass("message-success success");
              // set cookie lifetime on modalclosed
              if (!$.cookie("umNewslPopupCookieLifetime")) {
                setTimeout(function () {
                  $("#um-popup-newsletter")
                    .modal("closeModal")
                    .on("modalclosed", function () {
                      var date = new Date();
                      date.setTime(
                        date.getTime() + parseInt(cookieLifetime) * 1000
                      );
                      $.cookie("umNewslPopupCookieLifetime", true, {
                        expires: date,
                      });
                    });
                }, 2000);
              }
            }
            self.element.find(".messages").show();
            setTimeout(function () {
              self.element.find(".messages").hide();
            }, 5000);
          });
        }
        return false;
      });

      this._resetPopupStyleCss();
    },

    /**
     * Set width of the popup
     * @private
     */
    _setPopupStyleCss: function (width) {
      width = width || 600;
      /*height = height || 500;*/

      if (window.innerWidth > 786) {
        this.element
          .parent()
          .parent(".modal-inner-wrap")
          .css({
            "max-width": width + "px",
            /*height: height + "px",
            "background-image": "url('pub/media/banners/newsletter_bg.png')",*/
          });
      }
    },

    /**
     * Reset width of the popup
     * @private
     */
    _resetPopupStyleCss: function () {
      var self = this;
      $(window).resize(function () {
        if (window.innerWidth <= 786) {
          self.element
            .parent()
            .parent(".modal-inner-wrap")
            .css({ "max-width": "initial" });
        } else {
          self._setPopupStyleCss(self.options.innerWidth);
        }
      });
    },
  });
  return $.ulmod.processPopupNewsletter;
});
