document.addEventListener("DOMContentLoaded", function(event) {  
    $('.form').find('input').each(function() {
      var $this = $(this),
      label = $this.prev('label');

      label.removeClass('active highlight');
      if ($(this).val() != "") {
        label.addClass('active highlight');
      }
    });

    /* used in dashboard */
    var url = document.location.toString();
    if (url.match('#')) {
        var href = url.split('#')[1];
        $('a[href$="'+href+'"]').trigger('click');
    } 

    $('.form').find('input').on('keyup blur focus', function (e) {
      var $this = $(this),
          label = $this.prev('label');

        if (e.type === 'keyup') {
          if ($this.val() === '') {
              label.removeClass('active highlight');
            } else {
              label.addClass('active highlight');
            }
        } else if (e.type === 'blur') {
          if( $this.val() === '' ) {
            label.removeClass('active highlight'); 
          } else {
            label.removeClass('highlight');   
          }   
        } else if (e.type === 'focus') {
          
          if( $this.val() === '' ) {
            label.removeClass('highlight'); 
          } 
          else if( $this.val() !== '' ) {
            label.addClass('highlight');
          }
        }

    });

    /*$('.tab a').on('click', function (e) {
      e.preventDefault();
      
      $(this).parent().addClass('active');
      $(this).parent().siblings().removeClass('active');
      
      target = $(this).attr('href');
      alert(target);
      $('.tab-content > div').not(target).hide();
      
      $(target).fadeIn(600);
      
    });*/
});

$(".nav a").on("click", function(){
   $(".nav").find(".active").removeClass("active");
   $(this).parent().addClass("active");
});

$('.notif-badge').on('click', function() {
  $(this).removeClass('new');
});

var oldVal = "";
$('.form-control').on('focus', function() {
    if($(this).val() == "")
      {
        if(oldVal != "")
          oldVal = "";
      }
    else {
      oldVal = $(this).val();
      $(this).val('');
    }
});

$('.form-control').on('blur', function() {
    if($(this).val() != "")
      {
      }
    else {
      $(this).val(oldVal);
    }
});

$('.number').on('keypress', function(){
  return event.charCode >= 48 && event.charCode <= 57;
});

var updateNotifStatus = function(obj, uid, csrf, url) {  
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf
        }
    });

  $.ajax({
      type: "POST",
      url: url,
      data: {uid : uid},
      dataType: 'json',
      success: function (data) {
        if($('.notif-badge').hasClass('new'))
          $('.notif-badge').removeClass('new').html(0);
      },
      error: function (data) {
          console.log('Error:' + data);
      }
  });
  

  $('#myModal').modal('show');
/*  $('').*/
};

var check_va = function (val, uid, csrf, url) {
  $('.check_va_result').html('').removeClass('text-warning');
  if(val.toString().length < 11) {
    $('.check_va_result').addClass('text-warning').html('Account not found');
    $('#transferBtn').addClass('invalid-va');
    $('#transferBtn').attr('disabled', 'disabled');
  }
  else {
    $('#loader-va').show();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf
        }
    })

    $.ajax({
        type: "POST",
        url: url + "/getVAccount",
        data: {account : val.toString(), uid : uid},
        dataType: 'json',
        success: function (data) {
          if(data.error !== undefined){
            $( ".check_va_result" ).html( data.error ).addClass('text-warning');
            $('#transferBtn').addClass('invalid-va');
            $('#transferBtn').attr('disabled', 'disabled');
          }
          else {
            $('#transferBtn').removeClass('invalid-va');
            $( ".check_va_result" ).html( 'Account Name : ' + data.result );            
            
            if($('#transferBtn').hasClass('invalid-amt')) {
              $('#transferBtn').attr('disabled', 'disabled');              
            } else {
              $('#transferBtn').removeAttr('disabled');
            }
          }
          $('#loader-va').hide();
        },
        error: function (data) {
            console.log('Error:' + data);
        }
    });
  }
};

var check_amt = function (val, uid, csrf, url) {
  $('.check_amt_result').html('').removeClass('text-warning');
  if(parseFloat($('.user-deposit').data('amt')) - parseFloat(val) < 0) {
    $('.check_amt_result').addClass('text-warning').html('Not Enough Balance');
    $('#transferBtn').addClass('invalid-amt');
    $('#transferBtn').attr('disabled', 'disabled');
  }
  else {
    /* second check ! never ignore backend check */
    $('#loader-amt').show();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf
        }
    })

    $.ajax({
        type: "POST",
        url: url + "/getVAmount",
        data: {uid : uid, expense : val},
        dataType: 'json',
        success: function (data) {
          if(data.error !== undefined) {
            $( ".check_amt_result" ).html( data.error ).addClass('text-warning');
            $('#transferBtn').addClass('invalid-amt');
            $('#transferBtn').attr('disabled', 'disabled');
          }
          else {
            $('#transferBtn').removeClass('invalid-amt');
            $( ".check_amt_result" ).html( 'After this transaction, your wallet balance is Rp ' + data.result);
            
            if($('#transferBtn').hasClass('invalid-va')) {
              $('#transferBtn').attr('disabled', 'disabled');              
            } else {
              $('#transferBtn').removeAttr('disabled');
            }
          }

          $('#loader-amt').hide();
        },
        error: function (data) {
            console.log('Error:' + data);
        }
    });
  }
};