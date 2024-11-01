var embedCodeObject = new Object;
function getInternetExplorerVersion() {
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
            rv = parseFloat(RegExp.$1);
    }
    return rv;
}

function showEmbed(id) {
    
        var dims = getPageDims();
        
        var sp = getScrollPosition();
        
        var block = document.getElementById('embed_block');
        if(!block) {
            block = document.createElement('div')
            block.id = 'embed_block';
            block.className = 'post';
            block.style.position = "absolute";
			block.style.zIndex = 10001;
			block.style.width = "800px";
            block.innerHTML = '<div id="embedText"></div>' +
		'<textarea id="embedcode_text" cols="100" rows="10"></textarea>' +
		'<p><input class="widget_add_button" type="submit" onclick="hideEmbed();" value="Close"></p>';
		
             document.body.appendChild(block);
        } else {
            block.style.visibility = 'visible';
        }
        document.getElementById('embedcode_text').value = embedCodeObject[id]['embed_code'].replace(/<!-- return -->/g, '\n');
        document.getElementById('embedText').innerHTML = 'Embed code for ' + embedCodeObject[id]['name'] + ":";
        
        createTransparentDiv();
        
        if(getInternetExplorerVersion() > -1) {
                for(var widgetID in embedCodeObject) {
                        document.getElementById('widget' + widgetID).style.visibility = 'hidden';
                }
        }
        
        block.style.left = (sp.x + (dims.width - block.offsetWidth) / 2) + "px";
        block.style.top = (sp.y + (dims.height - block.offsetHeight) / 2) + "px";
}

function hideEmbed() {
        document.getElementById('embed_block').style.visibility = 'hidden';
        
        if(getInternetExplorerVersion() > -1) {
                for(var widgetID in embedCodeObject) {
                        document.getElementById('widget' + widgetID).style.visibility = 'visible';
                }
        }
        
        var transparentDiv = document.getElementById("transparentDiv");
        
        if(transparentDiv) {
                document.body.removeChild(transparentDiv);
        }
}

window.onresize = window.onscroll = function(event) {
        var dims = getPageDims();
        
        var sp = getScrollPosition();
        
        var transparentDiv = document.getElementById("transparentDiv");
        if(transparentDiv) {
                transparentDiv.style.left = sp.x + "px";
                transparentDiv.style.top = sp.y + "px";
                
                transparentDiv.style.width = dims.width + "px";
                transparentDiv.style.height = dims.height + "px";
        }
        
        var block = document.getElementById('embed_block')
        if(block) {
            block.style.left = (sp.x + (dims.width - block.offsetWidth) / 2) + "px";
            block.style.top = (sp.y + (dims.height - block.offsetHeight) / 2) + "px";
        }
}

function getPageDims() {
        var dims = new Object;
        var theWidth = 0;
        var theHeight = 0;
        if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
                theWidth = document.documentElement.clientWidth; 
                theHeight = document.documentElement.clientHeight;
        } else if (typeof (window.innerWidth) == 'number') {
                theWidth = window.innerWidth; 
                theHeight = window.innerHeight;
        } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
           theWidth = document.body.clientWidth; 
           theHeight = document.body.clientHeight;
        }
        
        return {width: theWidth, height: theHeight};
}

function getScrollPosition() {
        var dims = new Object;
        var theX = 0;
        var theY = 0;
        if (typeof (window.pageXOffset) == 'number') {
                theX = window.pageXOffset; 
                theY = window.pageYOffset;
        } else if (document.body  && (document.body.scrollLeft  || document.body.scrollTop )) {
                theX = document.body.scrollLeft; 
                theY = document.body.scrollTop;
        }
        
        return {x: theX, y: theY }
}

function createTransparentDiv() {
    var dims = getPageDims();
    var sp = getScrollPosition();
        
    var transparentDiv = document.createElement("div");
    transparentDiv.style.width = dims.width + "px";
    transparentDiv.style.height = dims.height + "px";
    if(getInternetExplorerVersion() == -1 || getInternetExplorerVersion() > 8.0) {
            transparentDiv.style.background = 'rgba(0,0,0,.2)';
    } else {
            transparentDiv.style.display = 'inline-block';
            transparentDiv.style.filter = "alpha(opacity=20)"
            transparentDiv.style.background = 'rgb(0,0,0)';
    }
    transparentDiv.style.left = sp['x'] + "px";
    transparentDiv.style.top = sp['y'] + "px";
    transparentDiv.style.position="absolute";
    transparentDiv.id='transparentDiv';
    transparentDiv.style.zIndex = 10000;
    
    document.body.appendChild(transparentDiv);
}