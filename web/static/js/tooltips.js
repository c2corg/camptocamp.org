function add_tooltips(css_class_to_observe)
{
    document.observe('dom:loaded', function(){
        $$(css_class_to_observe).each(function(obj){
            obj.observe('click', function(e){
            new Ajax.Updater('fields_tooltip',
                                    '/common/getinfo', 
                                    { asynchronous:true, 
                                      postBody: 'elt=' + obj.id,
                                      evalScripts:false, 
                                      method:'post', 
                                      onSuccess:function(request, json){
                                                        Element.hide('indicator');Element.show('fields_tooltip');
                                                        },
                                      onFailure:function(request, json){
                                                        Element.hide('indicator');
                                                        },
                                      onLoading:function(request, json){
                                                        Element.hide('fields_tooltip');Element.show('indicator');}
                                    }
                        );  
            });
        });
    });
}
