/*
Title:      	Admin Panel JavaScript
Author:     	Sam Rayner - http://samrayner.com
Created: 			2011-09-16
*/

PageNav = {
	sortableTree: function(parents) {
		var tree = {};
	
		for(var i = 0; i < parents.length; i++) {
			var parent = parents[i];
			tree[parent] = PageNav.sortableTree($("#"+parent+" > .sortable").sortable("toArray"));
			if(!$("#"+parent+" > * > input:checked").length)
				tree[parent]._hidden = true;
		}
		
		return tree;
	},
	
	updateOrder: function() {
		var topLevel = $("#page-list > ol").sortable("toArray");
		var tree =  PageNav.sortableTree(topLevel);
		$("#page_order").val(JSON.stringify(tree));
	},
	
	updateVisibility: function(event) {
		//get whether we're checking or unchecking
		var checking = event.target.checked;
	 
	 	//for all child checkboxes
		$(this).closest("li").find("* * input:checkbox").each(function() {
			//if we're unchecking, disable all children
			if(!checking)
				$(this).attr("disabled", true);
			
			//if checking	
			else {
				//get all grand-parent LIs
				$parentLis = $(this).closest("li").parentsUntil($("#page-list"),"li");
				
				var parentsUnchecked = $parentLis.children("div").children("input:not([checked])").length;
				
				//if we're checking, enable children who's parents are enabled
				if(!parentsUnchecked)
					$(this).removeAttr("disabled");
			}
		});
		
		PageNav.updateOrder();
	},
	
	labelTap: function() {
		var $checkbox = $("#".$(this).attr("for"));
		var checked = $checkbox.checked;
		
		if(checked)
			$checkbox.removeAttr("checked");
		else
			$checkbox.attr("checked", 1);
	},
	
	init: function() {
		$("#page-list input:checkbox").change(PageNav.updateVisibility);
		$("#page-list label").click(PageNav.labelTap);
	
		//disable text selection so we can drag
		$("#page-list > ol").disableSelection();
		
		//make lists sortable
		$(".sortable").sortable({
				update: PageNav.updateOrder
		});
		
		//fire change on every checkbox
		$(".sortable input:checkbox").change();
	}
}

Tooltip = {
	toggle: function() {
		var $message = $("#htaccess");
		$message.toggleClass("collapsed");
	},

	init: function() {
		$("#pretty_urls").change(Tooltip.toggle);
	}
}

Recache = {
	defaultText: null,
	interval: null,

	done: function() {
		window.clearInterval(Recache.interval);
		$("#recache-progress").remove();
		$("#recache-button").removeClass("active").html("Cache refresh complete");
	},

	get: function() {
		$jqxhr = $.get("recache/progress.php", function(current) {		
			$("#recache-button").html("Caching "+current+"&hellip;");
		});
	},

	click: function(event) {
		event.preventDefault();
		$(this).parent().append('<iframe id="recache-progress" src="recache/create_caches.php" style="display: none"></iframe>');
		$(this).addClass("active");
		Recache.get();
		Recache.interval = window.setInterval(Recache.get, 200);
	},
	
	init: function() {
		Recache.defaultText = $("#recache-button").html();
		$("#recache-button").click(Recache.click);
	}
}

$(function() {
	Tooltip.init();
	Recache.init();
	PageNav.init();
});