function start_loader() {
    $('body').append('<div id="preloader"><div class="loader-holder"><div></div><div></div><div></div><div></div>')
}

function end_loader() {
    $('#preloader').fadeOut('fast', function() {
        $('#preloader').remove();
    })
}
// function 
window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
    var Toast = Swal.mixin({
        toast: true,
        position: $pos || 'top-end',
        showConfirmButton: false,
        timer: 5000
    });
    Toast.fire({
        icon: $bg,
        title: $msg
    })
}

window.update_cart_count = function($count = 0) {
    if ($('#cart_count').length > 0) {
        if ($count > 0) {
            $('#cart_count').text($count).show();
        } else {
            $('#cart_count').text('0').hide();
        }
    }
}

window.uni_modal = function($title = '', $url = '', $size = '', $params = {}) {
    $('#uni_modal .modal-title').html($title)
    $('#uni_modal .modal-body').html('<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>')
    if($size != ''){
        $('#uni_modal .modal-dialog').addClass($size)
    }else{
        $('#uni_modal .modal-dialog').removeClass('modal-sm modal-md modal-lg modal-xl')
    }
    $('#uni_modal').modal('show')
    $.get($url, $params, function(resp){
        $('#uni_modal .modal-body').html(resp)
        if($('#uni_modal .modal-body script').length > 0){
            $('#uni_modal .modal-body script').each(function(){
                eval($(this).html())
            })
        }
    })
}

$(document).ready(function() {
    function startLockCountdown($form, $alertEl, lockedUntilTs) {
        try {
            if (!lockedUntilTs) return;
            var $btn = $form.find('button[type="submit"], .btn-primary').first();
            var endMs = (typeof lockedUntilTs === 'number' ? lockedUntilTs : parseInt(lockedUntilTs, 10)) * 1000;
            var timerId = null;
            var update = function() {
                var remaining = Math.max(0, endMs - Date.now());
                var totalSec = Math.floor(remaining / 1000);
                var mm = String(Math.floor(totalSec / 60)).padStart(2, '0');
                var ss = String(totalSec % 60).padStart(2, '0');
                var baseMsg = $alertEl.data('baseMsg') || $alertEl.text();
                $alertEl.data('baseMsg', baseMsg);
                $alertEl.text(baseMsg.replace(/(\s*\(\d{2}:\d{2}\))?$/, '') + ' (' + mm + ':' + ss + ')');
                if (remaining <= 0) {
                    clearInterval(timerId);
                    if ($btn.length) $btn.prop('disabled', false).text($btn.data('origText') || $btn.text());
                    $alertEl.remove();
                    $form.find('input').removeClass('is-invalid');
                }
            };
            if ($btn.length) {
                $btn.data('origText', $btn.text());
                $btn.prop('disabled', true).text('Locked');
            }
            update();
            timerId = setInterval(update, 1000);
        } catch (e) { console.log('Countdown init error', e); }
    }
    // Login (admin) - mirror client behavior for locked message
    $('#login-frm').submit(function(e) {
            e.preventDefault()
            var _this = $(this)
            if ($('.err_msg').length > 0)
                $('.err_msg').remove()
            var el = $('<div class="alert err_msg">')
            el.hide()
            start_loader()
            $.ajax({
                url: _base_url_ + 'classes/Login.php?f=login',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                error: err => {
                    console.log(err)
                    el.text('An error occured')
                    el.addClass('alert-danger')
                    _this.append(el)
                    el.show('slow')
                    end_loader()
                },
                success: function(resp) {
                    try {
                        if (typeof resp === 'string') {
                            resp = JSON.parse(resp)
                        }
                        if (resp.status == 'success') {
                            location.replace(_base_url_ + 'admin');
                        } else if (resp.status == 'locked') {
                            el.html("<i class='fa fa-lock'></i> " + (resp.msg || 'Account is locked.'))
                            el.addClass('alert-warning')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="username"]').focus()
                            if (resp.locked_until_ts) {
                                startLockCountdown(_this, el, resp.locked_until_ts)
                            }
                        } else if (resp.status == 'incorrect' || !!resp.msg) {
                            el.text(resp.msg || 'Incorrect username or password')
                            el.addClass('alert-danger')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="username"]').focus()
                        } else {
                            el.text('An error occured')
                            el.addClass('alert-danger')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="username"]').focus()
                        }
                    } catch (e) {
                        console.log('JSON Parse Error:', e, 'Response:', resp)
                        el.text('An error occured while processing the response')
                        el.addClass('alert-danger')
                        _this.append(el)
                        el.show('slow')
                        _this.find('input').addClass('is-invalid')
                        $('[name="username"]').focus()
                    }
                    end_loader()
                }
            })
        })
        //client login
    $('#clogin-frm').submit(function(e) {
            e.preventDefault()
            var _this = $(this)
            if ($('.err_msg').length > 0)
                $('.err_msg').remove()
            var el = $('<div class="alert err_msg">')
            el.hide()
            start_loader()
            $.ajax({
                url: _base_url_ + 'classes/Login.php?f=login_client',
                method: 'POST',
                data: $(this).serialize(),
                error: err => {
                    console.log(err)
                    el.text('An error occured')
                    el.addClass('alert-danger')
                    _this.append(el)
                    el.show('slow')
                    end_loader()
                },
                success: function(resp) {
                    try {
                        if (typeof resp === 'string') {
                            resp = JSON.parse(resp)
                        }
                        
                        if (resp.status == 'success') {
                            // Show success message before redirect
                            el.text('Login successful! Redirecting...')
                            el.addClass('alert-success')
                            _this.append(el)
                            el.show('slow')
                            
                            // Redirect after a short delay to show the success message
                            setTimeout(function() {
                                window.location.href = _base_url_;
                            }, 1000);
                        } else if (resp.status == 'locked') {
                            el.html("<i class='fa fa-lock'></i> " + (resp.msg || 'Account is locked.'))
                            el.addClass('alert-warning')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="email"]').focus()
                            if (resp.locked_until_ts) {
                                startLockCountdown(_this, el, resp.locked_until_ts)
                            }
                        } else if (!!resp.msg) {
                            el.text(resp.msg)
                            el.addClass('alert-danger')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="email"]').focus()
                        } else {
                            el.text('An error occured')
                            el.addClass('alert-danger')
                            _this.append(el)
                            el.show('slow')
                            _this.find('input').addClass('is-invalid')
                            $('[name="email"]').focus()
                        }
                    } catch (e) {
                        console.log('JSON Parse Error:', e, 'Response:', resp)
                        el.text('An error occured while processing the response')
                        el.addClass('alert-danger')
                        _this.append(el)
                        el.show('slow')
                        _this.find('input').addClass('is-invalid')
                        $('[name="email"]').focus()
                    }
                    end_loader()
                }
            })
        })
        // System Info
    $('#system-frm').submit(function(e) {
        e.preventDefault()
        start_loader()
        if ($('.err_msg').length > 0)
            $('.err_msg').remove()
        $.ajax({
            url: _base_url_ + 'classes/SystemSettings.php?f=update_settings',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            timeout: 30000, // 30 second timeout
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1000)
                } else {
                    $('#msg').html('<div class="alert alert-danger err_msg">An Error occurred while saving data</div>')
                    end_loader()
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error)
                if (status === 'timeout') {
                    $('#msg').html('<div class="alert alert-danger err_msg">Request timed out. Please try again.</div>')
                } else {
                    $('#msg').html('<div class="alert alert-danger err_msg">An error occurred: ' + error + '</div>')
                }
                end_loader()
            }
        })
    })
})