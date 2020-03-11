<div id="clickbank-cancellations-table" class="table-wrapper"></div>
<script type="text/template" id="clickbank-products-template">
	<h3 class="mt-4 mb-2"><%- data.label %></h3>
	<div class="table-wrapper -no-shadow">
		<table class="table table-striped" id="clickbank-cancellations-table">
			<colgroup>
				<col>
				<col width="25%">
				<col width="25%">
			</colgroup>
			<thead>
				<tr>
					<th>Access</th>
					<th class="text-center">Cancel at End of Subscription</th>
					<th class="text-center">Immediately Cancel After Subscription is Cancelled</th>
				</tr>
			</thead>
			<tbody>
				<% _.each(data.levels, function(level) { %>
				<tr class="button-hover" data-level="<%- level.id %>">
					<td><%- level.name %></td>
					<td class="text-center">
						<template class="wlm3-form-group">
							{
								name : 'cb_eot_cancel[<%- level.id %>]',
								value : 1,
								uncheck_value : 0,
								type : 'toggle-switch'
							}
						</template>
					</td>
					<td class="text-center">
						<template class="wlm3-form-group">
							{
								name : 'cb_scrcancel[<%- level.id %>]',
								value : 1,
								uncheck_value : 0,
								type : 'toggle-switch'
							}
						</template>
					</td>
				</tr>
				<% }); %>
			</tbody>
		</table>
	</div>
</script>

<script type="text/javascript">
	$('#clickbank-cancellations-table').empty();
	$.each(all_levels, function(k, v) {
		if(!Object.keys(v).length) return true;
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#clickbank-products-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#clickbank-cancellations-table').append(html);
	});
</script>