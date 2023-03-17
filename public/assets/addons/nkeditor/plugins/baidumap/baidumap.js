/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

// Baidu Maps: http://dev.baidu.com/wiki/map/index.php?title=%E9%A6%96%E9%A1%B5

KindEditor.plugin('baidumap', function (K) {
	var self = this, name = 'baidumap', lang = self.lang(name + '.');
	var mapWidth = K.undef(self.mapWidth, Math.min(document.body.clientWidth - 42, 558));
	var mapHeight = K.undef(self.mapHeight, 360);
	var getParam = function (name, url) {
		url = url || location.href;
		return url.match(new RegExp('[?&]' + name + '=([^?&]+)', 'i')) ? decodeURIComponent(RegExp.$1) : '';
	};
	self.clickToolbar(name, function () {
		if(!self.options.baiduMapKey){
			alert("请在配置中配置百度地图API密钥");
			return false;
		}
		var img = self.plugin.getSelectedImage();
		var src = img && img[0] ? $(img[0]).attr("src") : '';
		var center = getParam("center", src) || self.options.baiduMapCenter || '';
		var markers = getParam("markers", src);
		var html = ['<div class="ke-dialog-content-inner" style="padding-top: 0">',
			'<div class="ke-dialog-row ke-clearfix">',
			'<div class="ke-header">' + lang.address,
			'<input id="kindeditor_plugin_map_address" name="address" class="ke-input-text" value="" style="width:200px;" /> ',
			'<span>',
			'<input type="button" name="searchBtn" class="ke-button-common ke-button" value="' + lang.search + '" style="line-height:22px;padding:0 10px;" />',
			'</span>',
			'<input type="checkbox" id="keInsertDynamicMap" name="insertDynamicMap" class="checkbox" value="1" style="display:inline-block;" /> <label for="keInsertDynamicMap">' + lang.insertDynamicMap + '</label>',
			'</div>',
			'</div>',
			'<div class="ke-map" style="width:' + mapWidth + 'px;height:' + mapHeight + 'px;"></div>',
			'</div>'].join('');
		var dialog = self.createDialog({
			name: name,
			width: mapWidth + 42,
			title: self.lang(name),
			body: html,
			yesBtn: {
				name: self.lang('yes'),
				click: function (e) {
					var map = win.map;
					var centerObj = map.getCenter();
					var overlays = map.getOverlays();
					var markerArr = [];
					var point;
					if (overlays) {
						overlays.forEach(function (item) {
							if (item.point && item.isVisible() && !item.Fb) {
								point = item.point.lng + ',' + item.point.lat;
								if (markerArr.indexOf(point) < 0) {
									markerArr.push(point);
								}
							}
						});
					}
					var markers = markerArr.join("|");
					var center = centerObj.lng + ',' + centerObj.lat;
					var zoom = map.getZoom();
					var url = [checkbox[0].checked ? self.pluginsPath + 'baidumap/index.html' : 'https://api.map.baidu.com/staticimage',
						'?center=' + encodeURIComponent(center),
						'&zoom=' + encodeURIComponent(zoom),
						'&width=' + mapWidth,
						'&height=' + mapHeight,
						'&markers=' + encodeURIComponent(markers),
						'&markerStyles=' + encodeURIComponent('l,A')].join('');
					if (checkbox[0].checked) {
						self.insertHtml('<iframe src="' + url + '" frameborder="0" style="width:' + (mapWidth + 2) + 'px;height:' + (mapHeight + 2) + 'px;"></iframe>');
					} else {
						self.exec('insertimage', url);
					}
					self.hideDialog().focus();
				}
			},
			beforeRemove: function () {
				searchBtn.remove();
				if (doc) {
					doc.write('');
				}
				iframe.remove();
			}
		});
		var div = dialog.div,
			addressBox = K('[name="address"]', div),
			searchBtn = K('[name="searchBtn"]', div),
			checkbox = K('[name="insertDynamicMap"]', dialog.div),
			win, doc;
		var iframe = K('<iframe class="ke-textarea" frameborder="0" src="' + self.pluginsPath + 'baidumap/map.html?center=' + center + '&markers=' + markers + '&key=' + (self.options.baiduMapKey || "") + '" style="width:' + mapWidth + 'px;height:' + mapHeight + 'px;"></iframe>');

		function ready() {
			win = iframe[0].contentWindow;
			doc = K.iframeDoc(iframe);
		}

		iframe.bind('load', function () {
			iframe.unbind('load');
			if (K.IE) {
				ready();
			} else {
				setTimeout(ready, 0);
			}
		});
		K('.ke-map', div).replaceWith(iframe);
		searchBtn.click(function () {
			win.search(addressBox.val());
		});
	});
});
