<div id="learndash-groups-table" class="table-wrapper"></div>
<script type="text/template" id="learndash-groups-template">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Group</th>
				<th width="1%"><%= data.title %></th>
			</tr>
		</thead>
		<tbody>
			<% _.each(data, function(group) { %>
				<tr>
					<td><a href="#" data-toggle="modal" data-target="#learndash-group-<%- group.id %>"><%= group.title %></a</td>
					<td>
						<div class="btn-group-action">
							<a href="#" data-toggle="modal" data-target="#learndash-group-<%- group.id %>" class="btn -groups-btn" title="Edit Actions"><i class="wlm-icons md-24">edit</i></a>
						</div>
					</td>
				</tr>
			<% }); %>
		</tbody>
	</table>
</script>

<script type="text/javascript">
	$('#learndash-groups-table').empty();
	var groups = <?php echo json_encode($groups); ?>;
	var tmpl = _.template($('script#learndash-groups-template').html(), {variable: 'data'});
	var html = tmpl(groups);
	$('#learndash-groups-table').append(html);
</script>