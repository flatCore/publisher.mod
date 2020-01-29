<?php
/**
 * Modul publisher.mod
 */

if(!defined('FC_INC_DIR')) {
	die("No access");
}

?>

<link rel="stylesheet" href="../modules/publisher.mod/backend/assets/acp.css?v=46" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../modules/publisher.mod/backend/assets/bootstrap-datetimepicker.min.css" type="text/css" media="screen, projection">
<script type="text/javascript" src="../modules/publisher.mod/backend/assets/moment.min.js"></script>
<script type="text/javascript" src="../modules/publisher.mod/backend/assets/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="../modules/publisher.mod/backend/assets/accounting.min.js"></script>

<script type="text/javascript">
	
	$.extend(true, $.fn.datetimepicker.defaults, {
    icons: {
      time: 'far fa-clock',
      date: 'far fa-calendar',
      up: 'fas fa-arrow-up',
      down: 'fas fa-arrow-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right',
      today: 'fas fa-calendar-check',
      clear: 'far fa-trash-alt',
      close: 'far fa-times-circle'
    }
  });
  
	$(function(){
		
		$('.dp').datetimepicker({
			timeZone: 'UTC',
    	format: 'YYYY-MM-DD HH:mm'
  	});
  	
		$('.filter-images').keyup(function() {
		    var value = $(this).val();
		    var exp = new RegExp('^' + value, 'i');
				
		    $('.thumbnail').each(function() {
		        var isMatch = exp.test($('p:first', this).text());
		        $(this).toggle(isMatch);
		    });
		});


		if($("#price").val()) {
			
			get_netto = $("#price").val();
			get_tax = parseInt($('#tax').val());
			get_netto_calc = get_netto.replace(/\./g, '');
			get_netto_calc = get_netto_calc.replace(",",".");
			current_brutto = get_netto_calc*(get_tax+100)/100;
			current_brutto = accounting.formatNumber(current_brutto,4,".",",");
			$('#price_total').val(current_brutto);	
	
			$('#price').keyup(function(){
				get_netto = $('#price').val();
				get_tax = parseInt($('#tax').val());
				get_netto_calc = get_netto.replace(/\./g, '');
				get_netto_calc = get_netto_calc.replace(",",".");
				current_brutto = get_netto_calc*(get_tax+100)/100;
				current_brutto = accounting.formatNumber(current_brutto,4,".",",");
				$('#price_total').val(current_brutto);
			});			
			
			$('#price_total').keyup(function(){
				get_brutto = $('#price_total').val();
				get_tax = parseInt($('#tax').val());
				get_brutto_calc = get_brutto.replace(/\./g, '');
				get_brutto_calc = get_brutto_calc.replace(",",".");
				current_netto = get_brutto_calc*100/(get_tax+100);
				current_netto = accounting.formatNumber(current_netto,4,".",",");
				$('#price').val(current_netto);
			});
			
			$('#tax').keyup(function(){
				get_netto = $('#price').val();
				get_tax = parseInt($('#tax').val());
				get_netto_calc = get_netto.replace(",",".");

				current_brutto = get_netto_calc*(get_tax+100)/100;
				current_brutto = accounting.formatNumber(current_brutto,4,".",",");

				get_brutto_calc = current_brutto.replace(",",".");
				current_netto = get_brutto_calc*100/(get_tax+100);
				current_netto = accounting.formatNumber(current_netto,4,".",",");

				$('#price_total').val(current_brutto);
				$('#price').val(current_netto);
			});
		
		}
	
	
	});
	
</script>


