/**
 * $Id$
 */
addEvent(window, 'load', enableInfoTip);
addEvent(document, 'click', hideInfoTip);

function enableInfoTip()
{
    for (var i = 0; i < document.links.length; i ++)
    {
        if (document.links[i].rel.substr(0, 5).toLowerCase() == 'info_')
        {
            var params = document.links[i].rel.substr(5).split('_');
            var type = params.shift();
            
            document.links[i].infoType = type;
            document.links[i].infoParams = params;
            document.links[i].className += ' info_' + type;
            document.links[i].rel = '';
            document.links[i].href = '###';
            document.links[i].onclick = showInfoTip;
        }
    }
    
    var tips = document.getElementById('info_tip');
    if (!tips)
    {
        var tips = document.createElement('div');
        tips.id = 'info_tip';
        tips.className = 'info_tip';
        
        document.body.appendChild(tips);
    }
    
    var script_path = '';
    var scripts = document.getElementsByTagName("script");

    for (var i = 0; i < scripts.length; i ++)
    {
        if (scripts[i].src && scripts[i].src.match("info.js"))
        {
            script_path = scripts[i].src.replace(/info\.js.*$/, '');
            break;
        }
    }
    
    // load download_url.js
    if (typeof downloadUrl == 'undefined')
    {
        var script = document.createElement('script');
        script.type= 'text/javascript';
        script.src = script_path + 'download_url.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    }
    
    // load common.js
    if (typeof browser == 'undefined')
    {
        var script = document.createElement('script');
        script.type= 'text/javascript';
        script.src = script_path + 'common.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    }
}

function showInfoTip(e)
{
    e = e || window.event;

    var target   = e.target || e.srcElement;
    
    adjustPosition(target);

    currentShow = target;

    if (target.infoType == 'user')
    {
        showUser(target.infoParams);
    }

	if (target.infoType == 'ip')
	{
		showIP(target.infoParams);
	}

    return false;
}

function showUser(id)
{
    var tip = document.getElementById('info_tip');
    tip.className = 'user_tip';
    tip.innerHTML = '<div style="padding: 5px 8px" class="small">loading...</div>';

    downloadUrl('/info.php?act=user&id=' + id, function (data)
    {
        tip.innerHTML = '';
        tip.innerHTML = data;
        adjustPosition(currentShow);
    });
}

function showIP(ip)
{
    var tip = document.getElementById('info_tip');
    tip.className = 'user_tip';
    tip.innerHTML = '<div style="padding: 5px 8px" class="small">loading...</div>';

    downloadUrl('/info.php?act=ip&ip=' + ip, function (data)
    {
        tip.innerHTML = '';
        tip.innerHTML = data;
        adjustPosition(currentShow);
    });
}


function adjustPosition(target)
{
    var tip = document.getElementById('info_tip');  
    tip.style.display = 'block';
    
    var pos = getPosition(target);
    
    var x = pos.x + target.offsetWidth;
    var y = pos.y;
    
    if (pos.y - document.documentElement.scrollTop + tip.clientHeight > document.documentElement.clientHeight)
    {
        y = pos.y + target.offsetHeight - tip.clientHeight;
    }
    else
    {
        y = pos.y;
    }

    if (pos.x + tip.offsetWidth > document.documentElement.offsetWidth)
    {
        x = pos.x - tip.clientWidth;
    }

    tip.style.top = y + 'px';
    tip.style.left = x + 'px';
}

function hideInfoTip(e)
{
    e = e || window.event;

    var target   = e.target || e.srcElement;

    var tip = document.getElementById('info_tip');

    if (typeof target.infoType != 'undefined')
    {
        showInfoTip(e);
        return false;
    }

    if (target.id == 'info_tip')
    {
        return false;
    }
    
    var isLink = target.tagName.toLowerCase() == 'a' ? true : false;

    while (target.parentNode)
    {
        if (target.parentNode.id == 'info_tip')
        {
            return isLink ? true : false;
        }

        target = target.parentNode;
    }
	
	if (tip)
	{
		tip.style.display = 'none';
	}
    
    //return false;
}