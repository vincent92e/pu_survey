(function ($, Drupal, once, drupalSettings) {
    Drupal.behaviors.surveyModal = {
      attach: function (context, settings) {
        // Show logo image in a modal when button is clicked.
        once('loadSurveyModal', '.pu-button', context).forEach(function (element) {
            if (drupalSettings.pu_survey.image_src) {
                let modalHtml = `<div class="modal-wrapper"><div class="modal-content"><div class="close-modal">x</div><img src="${drupalSettings.pu_survey.image_src}" alt="Princeton Univerity logo"></div></div>`;
                let overlayHtml = '<div class="pu-overlay"></div>';
                $('body').append(modalHtml).append(overlayHtml);
            }

            $(element).click(function (event) {
                $('html', context).addClass('hide-overflow');
                $('.pu-overlay').css('display', 'block');
                $('.modal-wrapper', context).addClass('show-modal');
            })
        });

        // Close modal window.
        once('closeSurveyModal', '.close-modal', context).forEach(function (element) {
            $(element).click(function (event) {
                $('.modal-wrapper').removeClass('show-modal');
                $('.pu-overlay').css('display', 'none');
                $('html', context).removeClass('hide-overflow');
            })
        });
      }
    };
})(jQuery, Drupal, once, drupalSettings);