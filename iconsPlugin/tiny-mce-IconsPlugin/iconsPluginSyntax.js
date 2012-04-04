(function(){
	
	//tinymce.PluginManager.requireLangPack('IconsPlugin');
	
    tinymce.create('tinymce.plugins.IconsPlugin', {
    
        init : function(ed, url){
    	
	        //Add Button IconsPlugin
    		ed.addButton('iconsPlugin_button', {
	            title: 'iconsPlugin',
	            image: url + '/logo_small.png',
	            cmd: 'mceIconsPlugin'
	        });
    		
	        //Add new window with the list of IconsPlugin
            ed.addCommand('mceIconsPlugin', function() {
            	ed.windowManager.open({
	            		file : url + '/icons-list-select.php',
	            		width : 500 + parseInt(ed.getLang('IconsPlugin.delta_width', 0)),
	            		height : 590 + parseInt(ed.getLang('IconsPlugin.delta_height', 0)),
	            		inline : 1
	            		}, {
	            			plugin_url : url
            		});
            	});
       
           // ed.addShortcut('alt+ctrl+x', ed.getLang('iconsPlugin.php'), 'iconsPlugin');
			
        },
        createControl : function(n, cm){
            return null;
        },
        getInfo : function(){
            return {
                longname: 'Icons Plugin',
                author: '@eballo',
                authorurl: '',
                infourl: '',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('iconsPluginSyntax', tinymce.plugins.IconsPlugin);
})();