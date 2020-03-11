<div id="stripe-products-table" class="table-wrapper"></div>
<script type="text/template" id="stripe-products-template">
	<h3 class="mt-4 mb-2"><%= data.label %></h3>
	<table class="table table-striped">
		<colgroup>
			<col>
			<col width="250">
			<col width="100">
			<col width="1%">
		</colgroup>
		<thead>
			<tr>
				<th>Name</th>
				<th class="text-center">Amount/Stripe Plan</th>
				<th class="text-center">Button Code</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr class="button-hover">
				<td><a href="#" data-toggle="modal" data-target="#products-stripe-<%- level.id %>"><%= level.name %></a></td>
				<td class="text-center">
					<span id="stripe-product-amount-<%- level.id %>" class="stripe-onetime-<%- level.id %>"></span>
					<span id="stripe-product-plan-<%- level.id %>" class="stripe-plan-<%- level.id %>"></span>
				</td>
				<td class="text-center">
					<a href="" class="wlm-popover clipboard tight btn wlm-icons md-24 -icon-only" title="Copy Button Code" data-text="[wlm_stripe_btn sku=<%- level.id %>]"><span>code</span></a>
				</td>
				<td class="text-right">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#products-stripe-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>
