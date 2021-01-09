<?php

$bearerToken = $bearerToken ?? getBearerToken($code, $state);

?>

<input type="file" id="file-input" /> <button id="upload">Upload</button>

<div id="loading"></div>

<table id="asset-table">
</table>

<p>Accounts are limited as follows:</p>
<ul>
	<li>No more than 100 assets</li>
	<li>Each asset limited to 10485760 bytes (~10 MB)</li>
	<li>No more than 104857600 bytes total (~100 MB)</li>
</ul>

<script id="asset-row" type="text/x-handlebars-template">
	<tr>
		<th></th>
		<th>ID</th>
		<th>Mime</th>
		<th>Bytes</th>
		<th>Height</th>
		<th>Width</th>
		<th>Name</th>
		<th>Preview</th>
	</tr>
	{{#each assets}}
	<tr>
		<td>{{row_num}}</td>
		<td>{{asset_id}}</td>
		<td>{{mime_type}}</td>
		<td>{{file_size}}</td>
		<td>{{height}}</td>
		<td>{{width}}</td>
		<td><a href="{{link}}" target=”_blank”>{{name}}</a></td>
		<td>
			<div class="preview">
				{{#if is_image}}<a href="{{link}}" target=”_blank”><img src="{{link}}"></a>
				{{else}}<a href="{{link}}" target=”_blank”><img src="https://www.photoshelter.com/asset/image/txt_icon.png"></a>
				{{/if}}
			</div>
		</td>
	</tr>
	{{/each}}
	<tr>
		<td colspan="8">
		{{sumBytes}} bytes used ({{percentUsed}}%)
		</th>
	</tr>
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
		let bearerToken = '<?php echo $bearerToken ?>';
		let apiV3Key = '<?php echo $apiKey ?>';
		let pam = new PortalAssetManager(apiV3Key, bearerToken);
});
</script>

<script src="js/portal-asset-manager.js?ts=<?php echo time() ?>"></script>
