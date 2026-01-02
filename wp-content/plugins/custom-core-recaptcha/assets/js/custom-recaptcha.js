let enableRecaptcha = false;
let isOpenModal = false;

function ssCoreGenerateRecaptchaToken() {
    grecaptcha.ready(function () {
        grecaptcha.execute(recaptchaSettings.site_key, {
            action: 'submit'
        }).then(function (token) {
            enableRecaptcha = !!token;
            document.getElementById('ss-recaptcha-token').value = token;
            if ($('form.checkout').length > 0) {
                $('form.checkout').append(`<input type="hidden" name="wc-g-recaptcha-response" value="${token}">`)
            }
        });
    });
}

function initializeRecaptcha($) {

    if (recaptchaSettings.is_active_recaptcha != "1") return;

    if (recaptchaSettings.display_recaptcha != "1") $('.grecaptcha-badge').css('visibility', 'hidden');

    ssCoreGenerateRecaptchaToken();
}

jQuery(document).ready(function ($) {
    setTimeout(() => {
        initializeRecaptcha($);
    }, 100)
})
