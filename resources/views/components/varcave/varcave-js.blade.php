const generalLogLevel =  "{{ env('LOG_LEVEL') }}" ;
const caveShowTemplaceUrl = "{{route('varcave.caves.show', '__UUID__')}}";

$(document).ready(function() {
  // Check for click events on the navbar burger icon
  $(".navbar-burger").click(function() {
      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      $(".navbar-burger").toggleClass("is-active");
      $(".navbar-menu").toggleClass("is-active");

  });  

  $("#modal-message-button-cancel, .modal-message-close").on('click', function(){
    Logger.debug("close modal");
    closeModal( $(this).closest('.modal'), true );
  });

  //close message box
  $('#varcave-message-box .delete').on('click', function(e) {
    closeMessageBox();
  });

  //Theme changer
  $('.button-select-theme').on('click', function(e) {
      e.preventDefault();
      changeTheme($(this).data('theme'));
  });

  /* Dropdowns navbar on mobile */
  $('.navbar-item.has-dropdown > .navbar-link').on('click', function (e) {

    if ($(window).width() >= 1024) return;

    const $item = $(this).parent();

    // si le menu est fermé → on ouvre seulement
    if (!$item.hasClass('is-active')) {
      e.preventDefault();

      $('.navbar-item.has-dropdown').not($item).removeClass('is-active');
      $item.addClass('is-active');
    }
    //menu already opened 
  });

  /**
   * Handle session logout when disconnect button is clicked
   */
  $('#varcave-logout').on('click', function(e) {
    csrfToken = $(this).data('csrf-token');
    url = $(this).data('target-url');
    method = 'post';
    var data = {
        _token: csrfToken,
    };  
    sendAjaxRequest(url, method, data, 'success', 'error');
  });

  /**
   * Enable autocomplete on search input in naavbar
   */
    $("#quick-search-value").autocomplete({
      minLength: 2, // max char before request
      delay: 150,   // small delay to prevent server spamming
      source: "{{route('varcave.caves.quicksearch')}}",
      select: function( event, ui ) {
        Logger.debug( "Selected: " + ui.item.value + " aka " + ui.item.id );
        if (ui.item.uuid) {
          const target = caveShowTemplaceUrl.replace('__UUID__', ui.item.uuid);
          window.open(target, '_blank');
        }
      },
    });

    $('#quick-search-value').on('keydown', function (e) {
      if (e.key === 'Enter') {
          e.preventDefault();
      }
    });

    /**
     * Start result processing from quicksearch,  
     * redirect to search page with few args
     */
    $("#quick-search-button, #quick-search-value").on('click keydown', function(e) {
      if (e.type === 'keydown' && e.key !== 'Enter') {
        return;
      }

      const value = $("#quick-search-value").val();
      
      if(value != ''){
        window.location.href = "{{ route('varcave.caves.search') }}?quicksearch=1&type_name=LIKE&value_name=" + value;
      }
      Logger.info('Empty request');
      return false;

    })

});

/**
 * Handle ajax request to server
 * This custom ajax requester use `meta name="viewport" ` from <header> tag
*/
function sendAjaxRequest(url, method, data, onSuccess, onError) {
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content')
    },
    url: url,
    method: method,
    data: data,
    success: function(response) {
        Logger.info('Request succeed');
        if (typeof onSuccess === 'function') {
            onSuccess(response);
        }
        else if (typeof onSuccess === 'string') {
          if (onSuccess ==='silent')
          {
            return true;
          }
          else if(onSuccess === 'redirect'){
            Logger.info('redirect to given url');
            window.location.replace(response.redirectUrl);
          }
          else{
            showGenericSuccessMsg(onSuccess);
          }
        }
        else{
          Logger.info("redirect to homepage");
          window.location.replace('/');
        }
    },
    error: function(jqxhr, status, error){
      Logger.error('Resquest failed:');
      Logger.debug(jqxhr);
        if (typeof onError === 'function') {
            Logger.debug('Run function onError');
            onError(jqxhr);
        }
        else if (typeof onError === 'string') {
            if (onError ==='silent')
            {
              return false;
            }
            else {
                showGenericErrorMsg(onError);
            }
        } 
        else {
          showGenericErrorMsg("Generic AJAX error");
        }
    }
  });
};

function showProgress(duration, myelement){
  Logger.debug("Show progress bar");
  myelement.addClass("is-active");
  setTimeout(function() {
    myelement.removeClass('is-active');
    console.log("hide progress");
  }, duration);
}
 
/*
 * Close modal window
 */
function closeModal($el, clearContent = false){
  Logger.debug("Close modal window")
  $el.removeClass("is-active");
  if(clearContent === true)
  {
    clearModal($el);
  }
}

/*
 * Clear modal window content
 */
function clearModal($el){
  $el.find('#modal-message-title').empty();
  $el.find('#modal-message-body').empty();
  $el.find('#modal-message-buttons button:not(#modal-message-button-cancel)').remove();
}

/*
 * Show Modal window with title and body content
 */
function showModal(title, bodyContent, $el = $('#modal-message') ){
  Logger.debug('add modal window');
  $el.find('#modal-message-title').html(title);
  $el.find('#modal-message-body').html(bodyContent);
  $el.toggleClass('is-active');
}

/**
 * Display a message inside the Varcave message box component.
 *
 * This function is designed to handle both raw JSON responses and jQuery AJAX
 * error/success responses (responseJSON).
 * 
 *
 * @param {Object} response
 *   The response object. Can be:
 *   - a plain object containing `title`, `message`, `redirecturl`
 *   - a jQuery AJAX response containing `responseJSON`
 *      
 *   Each kind of response MUST contain an object of this type :
 *      {
 *        'title' : 'Displayed title',
 *        'message' : 'Main message body',
 *        'redirecturl : 'An optionnal redirection URL', 
 *      }
 *
 * @param {String} statusClass
 *   Bulma status class applied to the message box.
 *   Expected values: "is-success", "is-warning", "is-danger", etc.
 *   Default: "is-success"
 *
 * @param {Number} duration
 *   Time in milliseconds before the message box is hidden.
 *   Default: 3000 ms
 */
function showMessageBox(response, statusClass = "is-success", duration = 3000){
  Logger.info("show message in box");
  Logger.debug(response);

  const res = response?.responseJSON ?? response;

  Logger.debug('message:');
  Logger.debug(res.message);

  $("#varcave-message-box").removeClass('is-hidden  is-success  is-warning  is-danger');

  $("#varcave-message-box").addClass(statusClass);

  if (res.title ){ //title present and not null/false/empty
    $("#varcave-message-box-header").html(res.title);
  }else{
    $("#varcave-message-box-header").html('Error');
  }

  $("#varcave-message-box-body").html(res.message);
  setTimeout(function(){
    $("#varcave-message-box").addClass('is-hidden');
  }, duration)

  if(response.redirecturl && response.redirecturl !== ""){
    Logger.info('Redirect to given URL')
    setTimeout(function(){
      window.location.replace(response.redirecturl);
    }, duration + 500)
  }
}

function closeMessageBox(){
  $('#varcave-message-box').addClass('is-hidden');
  $('#varcave-message-box-header').html('');
  $('#varcave-message-box-body').html('');
}


function blinkElement($el, className = 'is-success', duration = 500, repeat = 3) {
    let count = 0;
    const interval = setInterval(() => {
        $el.toggleClass(className);
        count++;
        if (count >= repeat * 2) { // on/off → 2 in 2 steps
            clearInterval(interval);
            $el.removeClass(className); // force remove class
        }
    }, duration);
}

const Logger = (function () {
    const levels = ['debug', 'info', 'warn', 'error'];
    let currentLevel = generalLogLevel; // Minimal level to display

    function shouldLog(level) {
        return levels.indexOf(level) >= levels.indexOf(currentLevel);
    }

    return {
        setLevel: function (level) {
            if (levels.includes(level)) {
                currentLevel = level;
            } else {
                console.warn("bad level : " + level);
            }
        },
        debug: function (msg) {
            if (shouldLog('debug')) console.debug('[DEBUG] ' , msg);
        },
        info: function (msg) {
            if (shouldLog('info')) console.info('[INFO] ' , msg);
        },
        warn: function (msg) {
            if (shouldLog('warn')) console.warn('[WARN] ' , msg);
        },
        error: function (msg) {
            if (shouldLog('error')) console.error('[ERROR] ' , msg);
        }
    };
})();


/**
 * Clear or set values on form elements.
 * Argument must be a valid JQ selector
 * Ex : clearInputs([ ['#myInput'],['myInput2','mydefaultVal'] ])
 * @param {string|Array} items
 */
function clearInputs(items) {
    // Convert single string into an array of instructions
    let array = [];
    if (typeof items === 'string') {
        array.push([items]);
    } else if (Array.isArray(items)) {
        array = items;
    } else {
        Logger.warn('Invalid argument passed to clearInputs');
        return;
    }
    array.forEach(function(item) {
        let selector = item[0];
        let value = (item.length > 1) ? item[1] : undefined;
        let $el = $(selector);

        if ($el.length === 0) {
            Logger.warn(`Selector "${selector}" did not match any element.`);
            return;
        }

        // Handle checkbox
        if ($el.is(':checkbox')) {
            if (value === undefined) {
                $el.prop('checked', false); // uncheck by default
            } else {
                $el.prop('checked', (value === true || value === 'true'));
            }
        }
        // Handle radio buttons
        else if ($el.is(':radio')) {
            if (value !== undefined) {
                $el.each(function() {
                    $(this).prop('checked', $(this).val() == value);
                });
            } else {
                $el.prop('checked', false);
            }
        }
        // Handle select elements
        else if ($el.is('select')) {
            if (value === undefined) {
                $el.prop('selectedIndex', 0); // reset to first option
            } else {
                $el.val(value);
            }
        }
        // Handle other inputs and textarea
        else if ($el.is('input, textarea')) {
            if (value === undefined) {
                $el.val('');
            } else {
                $el.val(value);
            }
        }
    });
}

function showGenericErrorMsg(str){
    msg = {
      message: str,
      title: 'error',
    }
    showMessageBox(msg, 'is-danger', 5000);    
}

function showGenericSuccessMsg(str){
    showMessageBox(str, 'is-success', 5000);    
}

function changeTheme(theme){
  Logger.info('Change theme to:' + theme);
  switch (theme) {
    case "light":
      $('html').attr('data-theme', theme);
      data = theme;
      break;

    case 'dark':
      $('html').attr('data-theme', theme);
      break;
    
    default:
      //set system default theme
      Logger.debug('remove theme attribure');
      $('html').removeAttr('data-theme');
    
  }
  const url='{{ route('varcave.profile.theme.store') }}';
  var data = {
      theme: theme,
    };
  sendAjaxRequest(url, 'post', data, 'silent', 'silent');
}

function getTheme()
{
  return $('html').attr('data-theme');
}




