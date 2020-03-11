<p>
	<?php _e( 'Membership Levels can be assigned to Email Lists by selecting a List from the corresponding column below.', 'wishlist-member' ); ?>
</p>
<div id="constantcontactlists-table"></div>
<script type="text/template" id="constantcontactlists-template">
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
				<th width="40%">List</th>
				<th width="1%" nowrap>Unsubscribe if Removed from Level</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr>
				<td><a href="#" data-toggle="modal" data-target="#constantcontact-lists-modal-<%- level.id %>"><%= level.name %></a></td>
				<td id="constantcontact-lists-<%- level.id %>"></td>
				<td id="constantcontact-unsubscribe-<%- level.id %>"" class="text-center"></td>
				<td class="text-right" style="vertical-align: middle">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#constantcontact-lists-modal-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#constantcontactlists-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#constantcontactlists-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#constantcontactlists-table').append(html);
		return false;
	});
</script>