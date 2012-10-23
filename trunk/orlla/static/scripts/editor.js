/**
 * $Id: editor.js 842 2008-01-07 03:57:48Z legend $
 */

Editor = function (id, mode)
{
    this.id = id;
    this.textarea = null;
    this.toolbar = null;
    this.rows = new Array();
    this.cache = '';
    this.status = 'normal';
    this.mode = typeof(mode) == 'undefined' ? 'full' : 'lite';

    var self = this;
    
    if (document.all)
    {
        window.attachEvent('onload', function () {self.init()});
    }
    else
    {
        window.addEventListener('load', function () {self.init()}, false);
    }
}

Editor.prototype.init = function ()
{
    this.container = document.createElement("div");
    this.container.className = "editor_container";
    
    this.textarea = document.getElementById(this.id);
    //alert(this.textarea.offsetWidth);

    this.container.style.width = this.textarea.offsetWidth - 2 + 'px';

    this.textarea.className = 'editor_textarea';
    
    /*
    this.textarea.onkeydown = function ()
    {
        this.pos = document.selection.createRange().duplicate();
    }
    */        
            

    this.toolbar = document.createElement("div");
    this.toolbar.className = 'editor_toolbar';
    //this.toolbar.style.width  = this.textarea.offsetWidth - 2 + 'px';
    this.textarea.parentNode.insertBefore(this.toolbar, this.textarea);

    this.initToolbar();
    
    this.textarea.parentNode.insertBefore(this.container, this.textarea);

    this.container.appendChild(this.toolbar);
    this.container.appendChild(this.textarea);
    
    this.textarea.o_width  = parseInt(this.textarea.clientWidth);
    this.textarea.o_height = parseInt(this.textarea.clientHeight);


    Editor.editors[this.id] = this;
}


Editor.prototype.initToolbar = function()
{
    var self = this;

    for (var i = 0; i < Editor.rows.length; i ++)
    {
        var row = document.createElement("div");
        row.className = "eidtor_toolbar_row";

        for (var j = 0; j < Editor.rows[i].length; j ++)
        {
            switch (Editor.rows[i][j])
            {
                case '^':
                    var div = document.createElement("div");
                    div.className = 'editor_toolbar_start';
                    
                    var image = new Image();
                    image.src = Editor.toolbar_images_path + "toolbar.start.gif";
                    div.appendChild(image);
                    row.appendChild(div);
                    break;
                case '|':
                    var div = document.createElement("div");
                    div.className = 'editor_toolbar_separator';
                    var image = new Image();
                    image.src = Editor.toolbar_images_path + "toolbar.separator.gif";
                    div.appendChild(image);
                    row.appendChild(div);
                    break;
                case 'b':
                case 'i':
                case 'u':
                case 'center':
                case 'sup':
                case 'sub':
                case 'url':
                case 'email':
                case 'img':
                case 'code':
                case 'iframe':
                case 'quote':
                case 'zoom':
                    var a = document.createElement("a");
                    a.className = 'editor_toolbar_button';
                    a.id = "editor_toolbar_" + this.id + "_button_" + Editor.rows[i][j];
                    a.appendChild(this.getImage(Editor.rows[i][j]));
                    a.command = Editor.rows[i][j];
                    a.onclick = function (){self.execCommand(this.command)};
                    this.attachMouseEvent(a);

                    row.appendChild(a);
                    break;
                case 'color':
                    row.appendChild(this.makeSelect());
                    //this.makeColorSelect();
                    break;
                case 'smiles':
                    for (var k = 0; k < Editor.smiles.length; k ++)
                    {
                        var a = document.createElement("a");
                        a.className = 'editor_toolbar_button';
                        a.appendChild(this.getImage(Editor.smiles[k][2]));
                        a.smile = Editor.smiles[k][0];
                        a.title = Editor.smiles[k][2];
                        a.onclick = function (){self.execCommand('smile', this.smile)};
                        this.attachMouseEvent(a);

                        row.appendChild(a);
                    }

                    break;
                case "restore" :
                    var a = document.createElement("a");
                    a.className = 'editor_toolbar_button';

                    var div = document.createElement("div");
                    div.className = "editor_toolbar_button_image";
                    div.style.width = "60px";

                    var image = new Image()
                    image.src = Editor.toolbar_images_path + "toolbar_restore.gif";
                    
                    div.appendChild(image);

                    a.appendChild(div);
                    a.onclick = function (){self.execCommand('restore')};
                    this.attachMouseEvent(a);
                    
                    row.appendChild(a);
                    break;
            }
        }

        this.toolbar.appendChild(row);

        if (this.mode == 'lite')
        {
            break;
        }
    }
}


Editor.prototype.getImage = function(tag)
{
    var div = document.createElement("div");
    div.className = "editor_toolbar_button_image";

    var image = new Image()
    image.src = Editor.toolbar_images_path + "toolbar.images.gif";
    image.style.marginTop = "-" + Editor.imagelist[tag] * 16 + 'px';

    image.style.width = '16px';
    image.style.height = '1376px';

    div.appendChild(image);

    return div;
}

Editor.prototype.makeSelect = function ()
{
    var self = this;

    var div = document.createElement("div");
    div.className = 'editor_toolbar_select';

    var select = document.createElement("select");
    select.editor_id = this.id;
    select.onchange = function ()
    {
        if (this.options.selectedIndex != 0)
        {
            self.execCommand('color', this.options[this.options.selectedIndex].value);
        }           
    }

    var option = document.createElement('option');
    option.value = '';
    option.innerHTML = 'Color';
    select.appendChild(option);

    var colors = ['black', 'red', 'yellow', 'pink', 'green', 'orange', 'purple', 'blue', 'beige', 'brown', 'teal', 'navy', 'maroon', 'limegreen'];
    for (var i = 0; i < colors.length; i ++)
    {
        var option = document.createElement('option');
        option.value = colors[i];
        option.innerHTML = colors[i];
        option.style.color = colors[i];
        option.style.backgroundColor = colors[i];

        select.appendChild(option);
    }
    
    div.appendChild(select);

    return div;
}


Editor.prototype.makeSmiles = function ()
{
    var self = this;

    for (var i = 0; i < Editor.smiles.lenght; i ++)
    {
        var a = document.createElement("a");
        a.className = 'editor_toolbar_button';
        a.appendChild(this.getImage(Editor.smiles[i][2]));
        a.smiles = Editor.smiles[i][0];
        a.onclick = function (){self.execCommand('smile', this.smile)};
        
        this.cur_row.appendChild(a);
    }
}

Editor.prototype.attachMouseEvent = function (obj)
{
    obj.onmouseover = function ()
    {
        if (this.className != 'editor_toolbar_button_hold')
        {
            this.className = 'editor_toolbar_button_hover';
        }
    };

    obj.onmouseout = function ()
    {
        if (this.className != 'editor_toolbar_button_hold')
        {
            this.className = 'editor_toolbar_button';
        }
    };
}

Editor.prototype.execCommand = function (cmd, option)
{    
    if (cmd == 'smile')
    {
        this.insertText(option);
        return;
    }

    if (cmd == 'restore')
    {
        this.restoreData();
        return;
    }

    if (cmd == 'zoom')
    {
        this.zoom();
        return;
    }
    
    this.textarea.focus();

    var text = "[" + cmd;

    if (typeof option != 'undefined')
    {
        text += '=' + option;
    }

    text += ']';

    if(document.selection && document.selection.type == "Text")
    {
        text += document.selection.createRange().text + "[/" + cmd + "]";; 
    }
    else if(window.getSelection && this.textarea.selectionStart > -1)
    {
        var start = this.textarea.selectionStart; 
        var end   = this.textarea.selectionEnd;
        
        var value  = this.textarea.value;
        text  += value.substring(start, end) + "[/" + cmd + "]";
    }
    else
    {
        text   += "[/" + cmd + "]";
    }
    
    this.insertText(text);
}

Editor.prototype.insertText = function (text)
{        
    this.textarea.focus();

    if(document.selection)
    {
        if (document.selection.type == "Text")
        {
            var range = document.selection.createRange();

            range.text = text;
            range.moveStart("character", -text.length);
            range.select();
        }
        else
        {
            this.textarea.document.selection.createRange().text += text;
        }
    }
    else if(window.getSelection && this.textarea.selectionStart > -1)
    {
        var start = this.textarea.selectionStart; 
        var end   = this.textarea.selectionEnd;
        
        var value  = this.textarea.value;
        
        this.textarea.value = value.substring(0, start) + text +  value.slice(end);

        if (start != end)
        {
            this.textarea.selectionStart = start;
            this.textarea.selectionEnd = start + text.length;
        }
        else
        {
            this.textarea.selectionStart = this.textarea.selectionEnd = start + text.length;
        }
    }
    else
    { 
        this.textarea.value += text;
    }

    this.textarea.focus();
}

Editor.prototype.restoreData = function ()
{
    if (!window.confirm('恢复数据将覆盖当前内容，继续？'))
    {
        return;
    }

    var key = "save_data_" + this.id;

    var text = '';

    if (document.all)
    {
        with(document.documentElement)
        {
            try {
                load(key);
                text = getAttribute("value");
            }
            catch (e)
            {
                alert(e)
            }
        }
    }
    else if(window.sessionStorage)
    {
        try
        {
            text = sessionStorage.getItem(key)
        }
        catch (e)
        {
            alert(e);
        }
    }
    else
    {
        alert('您的浏览器不支持此功能');
    }

    if (text)
    {
        this.textarea.value = text;
    }
    else
    {
        alert('没有可恢复的数据!');
    }
}

Editor.prototype.saveData = function ()
{
    var key = "save_data_" + this.id;
    
    if (this.textarea.value == '')
    {
        return;
    }

    if (document.all)
    {
        with(document.documentElement)
        {
            try {
                load(key);
                setAttribute("value", this.textarea.value);
                save(key);
                return  getAttribute("value");
            }
            catch (e)
            {
                alert(e)
            }
        }
    }
    else if(window.sessionStorage)
    {
        try
        {
            sessionStorage.setItem(key, this.textarea.value)
        }
        catch (e)
        {
            alert(e);
        }
    }
    else
    {
        alert('您的浏览器不支持此功能');
    }
}


Editor.prototype.zoom = function ()
{    
    if (this.status == 'full')
    {
        with (this.container.style)
        {
            position = 'static';
            width = this.textarea.o_width +'px';
            height = this.textarea.o_height + this.toolbar.offsetHeight + 'px'            
        }

        with (this.textarea.style)
        {
            width = this.textarea.o_width - (document.all ? 4 : 2) + 'px';
            height = this.textarea.o_height - 5 + 'px'
        }

        this.status = 'normal';

        document.getElementById("editor_toolbar_" + this.id + "_button_zoom").className = "editor_toolbar_button";
    }
    else
    {
        with (this.container.style)
        {
            position = 'absolute';
            top = document.documentElement.scrollTop + 'px';
            left = 0;
            width = document.documentElement.clientWidth + 'px';
            height = document.documentElement.clientHeight + 'px'
        }

        with (this.textarea.style)
        {
            width = document.documentElement.clientWidth - (document.all ? 4 : 2) + 'px';
            height = this.container.offsetHeight - this.toolbar.offsetHeight - 5 + 'px'
        }

        this.status = 'full';

        document.getElementById("editor_toolbar_" + this.id + "_button_zoom").className = "editor_toolbar_button_hold";
    }
}


Editor.saveAll = function ()
{
    for (var id in Editor.editors)
    {
        Editor.editors[id].saveData();
    }
}

// static field
Editor.rows = new Array();
Editor.rows[0] = ['^', 'color', '|', 'b', 'i', 'u', '|', 'center', 'sup', 'sub', '|', 'url', 'email', 'img', '|', 'code', 'quote', '|', 'zoom', '|', 'restore'];
Editor.rows[1] = ['^', 'smiles'];

Editor.editors = new Object();
Editor.toolbar_images_path = MISC_PATH + 'images/editor/';

Editor.imagelist = {
    'b': 19,
    'i': 20,
    'u': 21,
    'center': 30,
    'sub': 23,
    'sup': 24,        
    'url': 33,
    'email': 35,
    'img': 36,
    'code': 0,
    'quote': 46,
    'zoom': 65,
    'smile': 67,
    'big smile': 68,
    'cool': 69,
    'blush': 70,
    'tongue': 71,
    'evil': 72,
    'wink': 73,
    'clown': 74,
    'black eye': 75,
    'sad': 76,
    'shy': 77,
    'shocked': 78,
    'angry': 79,
    'dead': 80,
    'sleepy': 81,
    'kisses': 82,
    'approve': 83,
    'disapprove': 84,
    'question': 85,
    'restore': 50
};
    
Editor.smiles = [
    [':)', 'smile.gif', 'smile'],
    [':D', 'smile_big.gif', 'big smile'],
    ['8D', 'smile_cool.gif', 'cool'],
    [':I', 'smile_blush.gif', 'blush'],
    [':P', 'smile_tongue.gif', 'tongue'],
    [':})', 'smile_evil.gif', 'evil'],
    [';)', 'smile_wink.gif', 'wink'],
    [':o)', 'smile_clown.gif', 'clown'],
    ['B)', 'smile_blackeye.gif', 'black eye'],
    [':(', 'smile_sad.gif', 'sad'],
    [':8)', 'smile_shy.gif', 'shy'],
    [':O)', 'smile_shock.gif', 'shocked'],
    [':!(', 'smile_angry.gif', 'angry'],
    ['xx(', 'smile_dead.gif', 'dead'],
    ['|)', 'smile_sleepy.gif', 'sleepy'],
    [':X', 'smile_kisses.gif', 'kisses'],
    [':^)', 'smile_approve.gif', 'approve'],
    [':~)', 'smile_disapprove.gif', 'disapprove'],
    [':?)', 'smile_question.gif', 'question']
];

// user data
if (document.all)
{
    document.documentElement.addBehavior("#default#userdata");

    window.attachEvent('onbeforeunload', Editor.saveAll);
}
else
{
    window.addEventListener('beforeunload', Editor.saveAll, false);
}