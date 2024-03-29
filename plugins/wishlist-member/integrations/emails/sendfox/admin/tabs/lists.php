<div id="sendfox-lists-table" class="table-wrapper"></div>
<script type="text/template" id="sendfox-lists-template">
	<table class="table table-striped">
		<colgroup>
			<col>
			<col width="40%">
			<!-- <col width="150"> -->
			<col width="1%">
		</colgroup>
		<thead>
			<tr>
				<th>Membership Level</th>
				<th>List Name</th>
				<!-- <th class="text-center">Unsubscribe if Removed from Level</th> -->
				<th></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data.levels, function(level) { %>
			<tr class="button-hover">
				<td><a href="#" data-toggle="modal" data-target="#sendfox-lists-modal-<%- level.id %>"><%= level.name %></a></td>
				<td id="sendfox-lists-<%- level.id %>"></td>
				<!-- <td id="sendfox-unsubscribe-<%- level.id %>" class="text-center"></td> -->
				<td class="text-right" style="vertical-align: middle">
					<div class="btn-group-action">
						<a href="#" data-toggle="modal" data-target="#sendfox-lists-modal-<%- level.id %>" class="btn -tags-btn" title="Edit"><i class="wlm-icons md-24">edit</i></a>
					</div>
				</td>
			</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#sendfox-lists-table').empty();
	$.each(all_levels, function(k, v) {
		var data = {
			label : post_types[k].labels.name,
			levels : v
		}
		var tmpl = _.template($('script#sendfox-lists-template').html(), {variable: 'data'});
		var html = tmpl(data);
		$('#sendfox-lists-table').append(html);
		return false;
	});
</script>