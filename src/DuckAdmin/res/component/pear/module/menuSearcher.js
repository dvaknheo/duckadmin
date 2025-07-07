layui.define([], function(exports) {
	"use strict";
	var MOD_NAME = 'menuSearcher';
	var menuSearcher = new function() {
		this.open = function (){
			layer.open({
				type: 1,
				offset: "10%",
				area: ['600px'],
				title: false,
				closeBtn: 0,
				shadeClose: true,
				anim: 0,
				move: false,
				content: _html,
				success: function(layero,layeridx){
                }

			})
        }
	};
	exports(MOD_NAME, menuSearcher);
})
