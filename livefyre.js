/**
 * @file
 *  Livefyre javascript behaviours.
 */

(function($) {

  Drupal.behaviors.livefyreAuthentication = {
    attach: function (context, settings) {
      $('#livefyre', context).once('livefyre', function () {
        $.getScript('//cdn.livefyre.com/Livefyre.js')
          .done(function () {
            // Then use Livefyre.require to add the App:
            Livefyre.require(['fyre.conv#3'], function(Conv) {
               new Conv(settings.livefyre.networkConfig, [settings.livefyre.convConfig], function(widget) {});
            });
          })
          .fail(function (jqxhr, settings, exception) {
            console.log(jqxhr);
            console.log(settings);
            console.log(exception);
          });
      });
    }
  };

})(jQuery);
