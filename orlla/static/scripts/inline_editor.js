/**
 * inline editor
 * 
 * $Id$
 */

function InlineEditor(id, name, url, extra)
{
    this.id    = id;    // element id
    this.url   = url;   // post url
    this.name  = name;  // field name
    
    this.method = 'post';
    this.text   = '<em>click here to edit</em>';
    this.form   = 'input';
    this.styles = {};

    this.oldvalue = '';

    var self = this;
    
    /*
    extra = {
        form: 'input/textarea',
        method: 'post/get',
        styles: {...},
        text: 'str',
    }
    */

    this.init = function(extra)
    {           
        if (typeof extra.form != 'undefined')
        {
            this.form = extra.form;
        }

        if (typeof extra.method != 'undefined')
        {
            this.method = extra.method;
        }

        if (typeof extra.styles != 'undefined')
        {
            this.styles = extra.styles;
        }

        if (typeof extra.text != 'undefined')
        {
            this.text = extra.text;
        }

        var element = document.getElementById(this.id);

        element.onmouseover = function () {this.style.backgroundColor = '#FFFFCC'};
        element.onmouseout  = function () {this.style.backgroundColor = ''};
        element.onclick     = function () {self.showEditor()};          
    }

    this.attachStyle = function()
    {
        //alert(document.getElementById(this.id).currentStyle.fontSize);
    }

    this.setStyle = function (name, value)
    {
        //
    }

    this.showEditor = function ()
    {
        //var editor  = InlineEditor.editors[this.id];
        var element = document.getElementById(this.id);
        
        this.oldvalue = element.innerHTML;

        var div_container = document.createElement('div');
        div_container.className = 'inline_editor';
        div_container.id = 'ie_' + this.id;

        var div_form = document.createElement('div');
        var div_toolbar = document.createElement('div');
        div_toolbar.className = "inline_editor_toolbar";

        if (this.form == 'input')
        {
            var form = document.createElement('input');
            form.type = 'text';
            form.style.width = element.offsetWidth - 8 + 'px';
            form.style.height = element.offsetHeight + 'px';
            form.style.lineHeight = element.offsetHeight + 'px';
        }
        else
        {
            var form = document.createElement('textarea');
            form.style.width = element.offsetWidth - 8 + 'px';
            form.style.height = '60px';
        }
        
        // extra styles
        if (this.styles)
        {
            for (var key in this.styles)
            {
                form.style[key] = this.styles[key];
            }
        }
        
        form.className = "inline_editor_form";
        form.id = 'ie_form_' + this.id;

        // html entity decode
        var value = element.innerHTML.toLowerCase().replace(/^\s+|\s+$/, '') == this.text.toLowerCase() ? '' : element.innerHTML;
        form.value = this.unEscapeHtml(value);
        
        div_form.appendChild(form);

        // toolbar
        var btnSubmit = document.createElement('input');
        btnSubmit.type = 'button';
        btnSubmit.value = 'SAVE';
        btnSubmit.onclick = function () {self.submit()};

        var btnCancel = document.createElement('input');
        btnCancel.type = 'button';
        btnCancel.value = 'Cancel';
        btnCancel.onclick = function () {self.cancel()};
        
        btnSubmit.className = "inline_editor_button";
        btnCancel.className = "inline_editor_button_gray";

        btnSubmit.editor_id = this.id;
        btnCancel.editor_id = this.id;

        var span_or = document.createElement("span");
        span_or.innerHTML = 'OR';
        span_or.style.padding = '0 5px';

        div_toolbar.appendChild(btnSubmit);
        div_toolbar.appendChild(span_or);
        div_toolbar.appendChild(btnCancel);

        div_container.appendChild(div_form);
        div_container.appendChild(div_toolbar);

        element.style.display = 'none';
        element.parentNode.insertBefore(div_container, element);
                
        form.focus();
    }

    this.submit = function ()
    {
        var element = document.getElementById(this.id);



        // submit data
        var data = document.getElementById('ie_form_' + this.id).value;
        var url = this.url; 
        url += this.url.indexOf("?") >= 0 ? "&" : "?";
        url += '&data=' + encodeURIComponent(data);
        
        if (typeof(downloadUrl) == 'undefined')
        {
            alert('function "downloadUrl" needed!');
            return;
        }
        
        element.innerHTML = '<span style="color: green">saving...</span>';
        downloadUrl(url, function (data){           
            self.parseResult(data);
        });
        

        element.style.display = 'block';
        element.parentNode.removeChild(document.getElementById('ie_' + this.id));
    }

    this.cancel = function ()
    {
        this.restore();

        var element = document.getElementById(this.id);

        element.style.display = 'block';
        element.parentNode.removeChild(document.getElementById('ie_' + this.id));

        // restore old value
    }

    this.parseResult = function (data)
    {
        var element = document.getElementById(this.id);
        this.restore();

        try
        {
            eval(data)
        }
        catch (e)
        {
            alert('unknow error');
            return;
        }

        if (typeof done == 'undefined')
        {
            alert('unknow error');
            return;
        }

        if (typeof error != 'undefined' && error != '')
        {
            alert(error);
            return;        
        }

        if (done)
        {           
            element.innerHTML = value == '' ? this.text : value;
        }
    }

    this.restore = function ()
    {
        var element = document.getElementById(this.id);
        element.innerHTML = this.oldvalue;
    }
    
    this.unEscapeHtml = function (str)
    {
        str = str.replace("&amp;", "&").replace("&#60;&#33;--", "<!--").replace("--&#62;", "-->").replace(/&#60;script/i, "<script");
        str = str.replace("&gt;", ">").replace("&lt;", "<").replace("&quot;", '"').replace("&#39;", "'");

        return str;
    }

    {
        if (typeof extra == 'undefined')
        {
            extra = {};
        }

        this.init(extra);
    }
}


//InlineEditor.downloadUrl = downloadUrl;