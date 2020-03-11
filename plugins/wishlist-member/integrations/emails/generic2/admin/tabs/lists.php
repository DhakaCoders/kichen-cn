<div class="row">
	<div class="col-md-12">
		<p>
			<a href="https://help.wishlistproducts.com/knowledge-base/generic-integration-email-providers/" target="_blank">Click Here for Documentation</a>
		</p>
	</div>
</div>
<div id="generic2-lists-table" class="table-wrapper -no-shadow"></div>
<script type="text/template" id="generic2-lists-template">
	<table class="table table-striped">
		<colgroup>
			<col>
			<col width="40%">
			<col width="150">
			<col width="1%">
		</colgroup>
		<thead>
			<tr>
				<th>Membership Level</th>
				<th>Autoresponder Subscription Form Post URL <?php $this->tooltip(__('The Post URL of the Subscription Form in your autoresponder.', 'wishlist-member')); ?></th>
				<th class="text-center">Unsubscribe if Removed from Level</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr class="button-hover">
				<td><a href="#" data-toggle="modal" data-target="#generic2-lists-modal-<%- level.id %>"><%= level.name %></a></td>
				<td id="generic2-lists-<%- level.id %>"></td>
				<td id="generic2-unsubscribe-<%- level.id %>" class="text-center"></td>
				<td class="text-right" style="vertical-align: middle">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#generic2-lists-modal-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#generic2-lists-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#generic2-lists-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#generic2-lists-table').append(html);
		return false;
	});
</script>