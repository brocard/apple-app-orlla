/**
 * $Id$
 */
Menu = function(handle_id, menu_id, method)
{
	var self = this;

	this.handle = document.getElementById(handle_id);
	this.menu   = document.getElementById(menu_id);
	this.timer = null;

	if (typeof method == 'undefined')
	{
		method = 'mouseover';
	}

	this.visitable = false;
	
	//addEvent(self.handle, method, function (){self.show(); return;});
	
	if (method == 'mouseover')
	{
		addEvent(self.handle, 'mouseover', function (){
			clearTimeout(self.timer);
			self.show();				
			return;
		});
		
		addEvent(self.handle, 'mouseout', function () {
			clearTimeout(self.timer);
			self.timer = setTimeout(function (){
				self.hide()
			}, 100);
		});

		addEvent(self.menu, 'mouseout', function () {
			if (self.timer)
			{
				return;
			}

			self.hide()
		});
		
		addEvent(self.menu, 'mouseover', function () {
			
			clearTimeout(self.timer);
			self.timer = null;

			self.show();
		});
	}
	else
	{
		addEvent(self.handle, method, function (){self.show(); return;});
		addEvent(document, method, function (e) {
			e = e || window.event;
			var target   = e.target || e.srcElement;			
			
			if (isChild(target, self.handle.id))
			{
				return;
			}
			
			if (!isChild(target, self.menu.id))
			{
				self.hide();
			}
		});
	}

	this.show = function ()
	{
		var pos = getPosition(this.handle);
		this.menu.style.display = 'block';
		this.menu.style.left = pos.x + 'px';
		this.menu.style.top  = pos.y + this.handle.offsetHeight + 'px';

		this.visitable = true;
	}

	this.hide = function ()
	{
		this.menu.style.display = 'none';

		this.visitable = false;
	}
}

Menu.menus = [];