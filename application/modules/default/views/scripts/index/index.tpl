<form action="/parse" method="post">
	<table>
		<tr>
			<td><label for="domain-name">Domain name: </label></td>	
			<td><input id="domain-name" name="domain-name" type="text" value=""/></td>
		</tr>
		<tr>
			<td><label for="email">Email: </label></td>	
			<td><input id="email" name="email" type="text" value=""/></td>
		</tr>
		<tr>
			<td>&nbsp;</td>	
			<td><input name="submit" type="submit" value="Parse"/></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('form [name="submit"]').click(function(){
			if (jQuery('#domain-name').val() == '' || jQuery('#email').val() == '') {
	        	alert("Please complete the form");
				return false;
			}
			
			var pattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i;
            if(!pattern.test(jQuery('#domain-name').val())){
            	alert("Domain name is not valid");
            	return false;
            }
	        
			var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
            if(!pattern.test(jQuery('#email').val())){
            	alert("Email is not valid");
            	return false;
            }
		});
	});
</script>