Number.prototype.NaN0 = function ()
{
    return isNaN(this) ? 0 : this;
}

if (!Array.prototype.push)
{
    Array.prototype.push = function (item)
    {
        this[this.length] = item;
    }
}

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

var currentWidth = window.innerWidth;

function addEvent(o, e, m)
{
    if (browser.isIE)
    {
        o.attachEvent('on' + e, m);
    }
    else
    {
        o.addEventListener(e, m, false);
    }
}

function setOpacity(obj, opacity)
{
    if (document.all)
    {
        obj.style.filter = 'alpha(opacity=' + opacity * 100 + ')';
    }
    else
    {
        obj.style.opacity = opacity;
    }
}

browser = new function ()
{
    this.isIE    = false;
    this.isOP    = false;
    this.isNS    = false;

    var userAgent = navigator.userAgent;

    if (userAgent.indexOf("MSIE") != -1 && document.all)
    {
        this.isIE = true;
        return;
    }

    if (window.opera)
    {
        this.isOP = true;
        return;
    }

    if (userAgent.indexOf('Gecko') != -1 || userAgent.indexOf('Netscape6/') != -1)
    {
        this.isNS = true;
        return;
    }
}

function getPosition(obj)
{
    var left = 0;
    var top  = 0;

    while (obj.offsetParent)
    {
        left += obj.offsetLeft + (obj.currentStyle ? (parseInt(obj.currentStyle.borderLeftWidth)).NaN0() : 0);
        top  += obj.offsetTop  + (obj.currentStyle ? (parseInt(obj.currentStyle.borderTopWidth)).NaN0() : 0);
        obj   = obj.offsetParent;
    }

    left += obj.offsetLeft + (obj.currentStyle ? (parseInt(obj.currentStyle.borderLeftWidth)).NaN0() : 0);
    top  += obj.offsetTop  + (obj.currentStyle ? (parseInt(obj.currentStyle.borderTopWidth)).NaN0() : 0);

    return {x:left, y:top};
}

function isChild(obj, pid)
{
	var obj = typeof(obj) != 'object' ? document.getElementById(obj) : obj;
	
	if (obj.id == pid)
	{
		return true;
	}

	while (obj.parentNode)
	{
		if (obj.parentNode.id == pid)
		{
			return true;
		}

		obj = obj.parentNode;
	}

	return false;
}