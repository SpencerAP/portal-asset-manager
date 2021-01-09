PortalAssetManager = function(apiV3Key, bearerToken) {
	this.apiV3Key = apiV3Key;
	this.bearerToken = bearerToken;
	this.init();
};

PortalAssetManager.prototype = {
	apiV3Base: 'https://www.photoshelter.com/psapi/v3.0/',
	apiV3Key: null,
	authToken: null,
	bearerToken: null,
	userId: null,
	orgId: null,
	tplCache: {},
	loadingElem: document.querySelector('#loading'),
	uploadElem: document.querySelector('#upload'),
	fileInputElem: document.querySelector('#file-input'),
	availBytes: 104857600,

	init: async function() {
		this.clearCode();
		this.userId = await this.getUserId();
		this.refreshAssets();
		this.uploadElem.addEventListener('click', event => this.upload(event));
	},

	// clear the oauth code from the URL bar, it's no longer usable
	clearCode: function() {
		if (!window.location.search.includes('code')) {
			return;
		}

		let url = new URL(window.location.origin + window.location.pathname);
		window.history.pushState({}, '', url);
	},

	upload: async function(clickEvent) {
		if (!this.fileInputElem.value) {
			alert('No file chosen');
			return;
		}

		this.uploadElem.disabled = true;
		this.fileInputElem.disabled = true;

		let formData = new FormData();
		formData.append('file', this.fileInputElem.files[0]);

		this.loadingElem.textContent = 'Uploading...';

		let response = await this.apiV3Fetch('POST', 'mem/asset/upload', formData);

		this.uploadReset();
		this.refreshAssets();
	},

	uploadReset: function() {
		let newInput = document.createElement('input');
		newInput.id = this.fileInputElem.id;
		newInput.type = 'file';
		this.fileInputElem.parentNode.replaceChild(newInput, this.fileInputElem);
		this.fileInputElem = newInput;

		this.uploadElem.disabled = false;
	},

	refreshAssets: async function() {
		this.loadingElem.textContent = 'Querying assets...';

		let sumBytes = 0;
		let assets = await this.getAssets();

		for (let i = 0, link; i < assets.length; i++) {

			// sadly the links aren't available as an extend,
			// we have to query for each one
			link = await this.getAssetLink(assets[i].asset_id);
			// API does not return HTTPS links,
			// though HTTPS is supported. Lame!
			assets[i].link = link.replace('http://', 'https://');

			assets[i].is_image = assets[i].mime_type.includes('image/');
			assets[i].row_num = i + 1;
			sumBytes += parseInt(assets[i].file_size);
		}

		let percentUsed = (sumBytes/this.availBytes) * 100;
		percentUsed = Math.round((percentUsed + Number.EPSILON) * 100) / 100; // fancy rounding

		this.render('#asset-row', '#asset-table', {assets, sumBytes, percentUsed});

		this.loadingElem.textContent = 'Ready.';
	},

	apiV3Fetch: async function(method, url, data = {}) {
		let headers = new Headers();
		headers.append('X-PS-Api-Key', this.apiV3Key);
		headers.append('Authorization', 'Bearer ' + this.bearerToken);

		let fetchOpt = {
			method: method,
			headers: headers,
		};

		if (method === 'GET') {
			let queryString = Object.keys(data).map(key => key + '=' + data[key]).join('&');
			url += '?' + queryString;
		}
		else {
			fetchOpt.body = data;
		}

		let response = await fetch(this.apiV3Base + url, fetchOpt);
		let result = await response.json();

		return result;
	},

	getUserId: async function() {
		let response = await this.apiV3Fetch('GET', 'mem/user/session');
		return response.data.Session.user_id;
	},

	getAssets: async function() {
		let response = await this.apiV3Fetch('GET', 'asset/query', {user_id: this.userId});
		await this.getAssetLink(response.data.Asset[0].asset_id);
		return response.data.Asset;
	},

	getAssetLink: async function(assetId) {
		let response = await this.apiV3Fetch('GET', 'asset/' + assetId + '/link');
		return response.data.link;
	},

	render: function(sourceID, targetID, context) {
		let template, source, html;

		if (!!this.tplCache[sourceID]) {
			template = this.tplCache[sourceID];
		}
		else {
			source = document.querySelector(sourceID).innerHTML;
			template = Handlebars.compile(source);
			this.tplCache[sourceID] = template;
		}

		html = template(context);
		document.querySelector(targetID).innerHTML = html;
	}
}
