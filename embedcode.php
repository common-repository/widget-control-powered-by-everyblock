<?php
    function get_html_for_embed($id, $embedCode, $jsEmbedCode, $name, $pageId) {
        return '<div class="widget_block">'.
                            '<div class="widget_img" id="widget' . $id .'">' . stripslashes($embedCode) .'</div>' . 
                            '<div class="widget_add"><input class="widget_add_button" onclick="showEmbed('. $id .')" type="submit" value="Add Now" />'.
                            '</div>'.
                            '</div>'.
                                    '<script language="javascript">'.
                                            'embedCodeObject['. $id .'] = new Object;' . 
                                            'embedCodeObject[' . $id .']["name"] = "' . $name .'";' . 
                                            'embedCodeObject[' . $id .']["embed_code"] = "'. $jsEmbedCode . '";' . 
                                    '</script>';
                                    
        
    }
    
    function get_javacript_for_popups() {
        return ('<script language="javascript" src="'. plugins_url( 'lightbox.js' , __FILE__ ) . '"></script>');
    }
    ?>