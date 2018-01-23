;(function($) {
/*
    * ui.dropdownchecklist
    *
    * Copyright (c) 2008-2010 Adrian Tosca, Copyright (c) 2010-2011 Ittrium LLC
    * Dual licensed under the MIT (MIT-LICENSE.txt) OR GPL (GPL-LICENSE.txt) licenses.
    *
*/
    // The dropdown check list jQuery plugin transforms a regular select html element into a dropdown check list.
    $.widget("ui.dropdownchecklist", {
        // Some globlals
        // $.ui.dropdownchecklist.gLastOpened - keeps track of last opened dropdowncheck list so we can close it
        // $.ui.dropdownchecklist.gIDCounter - simple counter to provide a unique ID as needed
        version: function() {
            alert('DropDownCheckList v1.4');
        },        
        // Creates the drop container that keeps the items and appends it to the document
        _appendDropContainer: function( controlItem ) {
            var wrapper = $("<div/>");
            // the container is wrapped in a div
            wrapper.addClass("ui-dropdownchecklist ui-dropdownchecklist-dropcontainer-wrapper");
            wrapper.addClass("ui-widget");
            // assign an id
            wrapper.attr("id",controlItem.attr("id") + '-ddw');
            // initially positioned way off screen to prevent it from displaying
            // NOTE absolute position to enable width/height calculation
            wrapper.css({ position: 'absolute', left: "-33000px", top: "-33000px"  });
            
            var container = $("<div/>"); // the actual container
            container.addClass("ui-dropdownchecklist-dropcontainer ui-widget-content");
            container.css("overflow-y", "auto");
            wrapper.append(container);
            
            // insert the dropdown after the master control to try to keep the tab order intact
            // if you just add it to the end, tabbing out of the drop down takes focus off the page
            // @todo 22Sept2010 - check if size calculation is thrown off if the parent of the
            //        selector is hidden.  We may need to add it to the end of the document here, 
            //        calculate the size, and then move it back into proper position???
            //$(document.body).append(wrapper);
            wrapper.insertAfter(controlItem);

            // flag that tells if the drop container is shown or not
            wrapper.isOpen = false;
            return wrapper;
        },
        // Look for browser standard 'open' on a closed selector
        _isDropDownKeyShortcut: function(e,keycode) {
            return e.altKey && ($.ui.keyCode.DOWN == keycode);// Alt + Down Arrow
        },
        // Look for key that will tell us to close the open dropdown
        _isDropDownCloseKey: function(e,keycode) {
            return ($.ui.keyCode.ESCAPE == keycode) || ($.ui.keyCode.ENTER == keycode);
        },
        // Handler to change the active focus based on a keystroke, moving some count of
        // items from the element that has the current focus
        _keyFocusChange: function(target,delta,limitToItems) {
            // Find item with current focus
            var focusables = $(":focusable");
            var index = focusables.index(target);
            if ( index >= 0 ) {
                index += delta;
                if ( limitToItems ) {
                    // Bound change to list of input elements
                    var allCheckboxes = this.dropWrapper.find("input:not([disabled])");
                    var firstIndex = focusables.index(allCheckboxes.get(0));
                    var lastIndex = focusables.index(allCheckboxes.get(allCheckboxes.length-1));
                    if ( index < firstIndex ) {
                        index = lastIndex;
                    } else if ( index > lastIndex ) {
                        index = firstIndex;
                    }
                }
                focusables.get(index).focus();
            }
        },
        // Look for navigation, open, close (wired to keyup)
        _handleKeyboard: function(e) {
            var self = this;
            var keyCode = (e.keyCode || e.which);
            if (!self.dropWrapper.isOpen && self._isDropDownKeyShortcut(e, keyCode)) {
                // Key command to open the dropdown
                e.stopImmediatePropagation();
                self._toggleDropContainer(true);
            } else if (self.dropWrapper.isOpen && self._isDropDownCloseKey(e, keyCode)) {
                // Key command to close the dropdown (but we retain focus in the control)
                e.stopImmediatePropagation();
                self._toggleDropContainer(false);
                self.controlSelector.focus();
            } else if (self.dropWrapper.isOpen 
                    && (e.target.type == 'checkbox')
                    && ((keyCode == $.ui.keyCode.DOWN) || (keyCode == $.ui.keyCode.UP)) ) {
                // Up/Down to cycle throught the open items
                e.stopImmediatePropagation();
                self._keyFocusChange(e.target, (keyCode == $.ui.keyCode.DOWN) ? 1 : -1, true);
            } else if (self.dropWrapper.isOpen && (keyCode == $.ui.keyCode.TAB) ) {
                // I wanted to adjust normal 'tab' processing here, but research indicates
                // that TAB key processing is NOT a cancelable event. You have to use a timer
                // hack to pull the focus back to where you want it after browser tab
                // processing completes.  Not going to work for us.
                //e.stopImmediatePropagation();
                //self._keyFocusChange(e.target, (e.shiftKey) ? -1 : 1, true);
           }
        },
        // Look for change of focus
        _handleFocus: function(e,focusIn,forDropdown) {
            var self = this;
            if (forDropdown && !self.dropWrapper.isOpen) {
                // if the focus changes when the control is NOT open, mark it to show where the focus is/is not
                e.stopImmediatePropagation();
                if (focusIn) {
                    self.controlSelector.addClass("ui-state-hover");
                    if ($.ui.dropdownchecklist.gLastOpened != null) {
                        $.ui.dropdownchecklist.gLastOpened._toggleDropContainer( false );
                    }
                } else {
                    self.controlSelector.removeClass("ui-state-hover");
                }
               } else if (!forDropdown && !focusIn) {
                   // The dropdown is open, and an item (NOT the dropdown) has just lost the focus.
                   // we really need a reliable method to see who has the focus as we process the blur,
                   // but that mechanism does not seem to exist.  Instead we rely on a delay before
                   // posting the blur, with a focus event cancelling it before the delay expires.
                if ( e != null ) { e.stopImmediatePropagation(); }
                self.controlSelector.removeClass("ui-state-hover");
                self._toggleDropContainer( false );                
               }
        },
        // Clear the pending change of focus, which keeps us 'in' the control
        _cancelBlur: function(e) {
            var self = this;
            if (self.blurringItem != null) {
                clearTimeout(self.blurringItem);
                self.blurringItem = null;
            } 
        },
        // Creates the control that will replace the source select and appends it to the document
        // The control resembles a regular select with single selection
        _appendControl: function() {
            var self = this, sourceSelect = this.sourceSelect, options = this.options;

            // the control is wrapped in a basic container
            // inline-block at this level seems to give us better size control
            var wrapper = $("<span/>");
            wrapper.addClass("ui-dropdownchecklist ui-dropdownchecklist-selector-wrapper ui-widget");
            wrapper.css( { display: "inline-block", cursor: "default", overflow: "hidden" } );
            
            // assign an ID 
            var baseID = sourceSelect.attr("id");
            if ((baseID == null) || (baseID == "")) {
                baseID = "ddcl-" + $.ui.dropdownchecklist.gIDCounter++;
            } else {
                baseID = "ddcl-" + baseID;
            }
            wrapper.attr("id",baseID);
            
            // the actual control which you can style
            // inline-block needed to enable 'width' but has interesting problems cross browser
            var control = $("<span/>");
            control.addClass("ui-dropdownchecklist-selector ui-state-default");
            control.css( { display: "inline-block", overflow: "hidden", 'white-space': 'nowrap'} );
            // Setting a tab index means we are interested in the tab sequence
            var tabIndex = sourceSelect.attr("tabIndex");
            if ( tabIndex == null ) {
                tabIndex = 0;
            } else {
                tabIndex = parseInt(tabIndex);
                if ( tabIndex < 0 ) {
                    tabIndex = 0;
                }
            }
            control.attr("tabIndex", tabIndex);
            control.keyup(function(e) {self._handleKeyboard(e);});
            control.focus(function(e) {self._handleFocus(e,true,true);});
            control.blur(function(e) {self._handleFocus(e,false,true);});
            wrapper.append(control);

            // the optional icon (which is inherently a block) which we can float
            if (options.icon != null) {
                var iconPlacement = (options.icon.placement == null) ? "left" : options.icon.placement;
                var anIcon = $("<div/>");
                anIcon.addClass("ui-icon");
                anIcon.addClass( (options.icon.toOpen != null) ? options.icon.toOpen : "ui-icon-triangle-1-e");
                anIcon.css({ 'float': iconPlacement });
                control.append(anIcon);
            }
            // the text container keeps the control text that is built from the selected (checked) items
            // inline-block needed to prevent long text from wrapping to next line when icon is active
            var textContainer = $("<span/>");
            textContainer.addClass("ui-dropdownchecklist-text");
            textContainer.css( {  display: "inline-block", 'white-space': "nowrap", overflow: "hidden" } );
            control.append(textContainer);

            // add the hover styles to the control
            wrapper.hover(
                function() {
                    if (!self.disabled) {
                        control.addClass("ui-state-hover");
                    }
                }
            ,     function() {
                    if (!self.disabled) {
                        control.removeClass("ui-state-hover");
                    }
                }
            );
            // clicking on the control toggles the drop container
            wrapper.click(function(event) {
                if (!self.disabled) {
                    event.stopImmediatePropagation();
                    self._toggleDropContainer( !self.dropWrapper.isOpen );
                }
            });
            wrapper.insertAfter(sourceSelect);

            // Watch for a window resize and adjust the control if open
            $(window).resize(function() {
                if (!self.disabled && self.dropWrapper.isOpen) {
                    // Reopen yourself to get the position right
                    self._toggleDropContainer(true);
                }
            });       
            return wrapper;
        },
        // Creates a drop item that coresponds to an option element in the source select
        _createDropItem: function(index, tabIndex, value, text, optCss, checked, disabled, indent) {
            var self = this, options = this.options, sourceSelect = this.sourceSelect, controlWrapper = this.controlWrapper;
            // the item contains a div that contains a checkbox input and a lable for the text
            // the div
            var item = $("<div/>");
            item.addClass("ui-dropdownchecklist-item");
            item.css({'white-space': "nowrap"});
            var checkedString = checked ? ' checked="checked"' : '';
            var classString = disabled ? ' class="inactive"' : ' class="active"';
            
            // generated id must be a bit unique to keep from colliding
            var idBase = controlWrapper.attr("id");
            var id = idBase + '-i' + index;
            var checkBox;
            
            // all items start out disabled to keep them out of the tab order
            if (self.isMultiple) { // the checkbox
                checkBox = $('<input disabled type="checkbox" id="' + id + '"' + checkedString + classString + ' tabindex="' + tabIndex + '" />');
            } else { // the radiobutton
                checkBox = $('<input disabled type="radio" id="' + id + '" name="' + idBase + '"' + checkedString + classString + ' tabindex="' + tabIndex + '" />');
            }
            checkBox = checkBox.attr("index", index).val(value);
            item.append(checkBox);
            
            // the text
            var label = $("<label for=" + id + "/>");
            label.addClass("ui-dropdownchecklist-text");
            if ( optCss != null ) label.attr('style',optCss);
            label.css({ cursor: "default" });
            label.html(text);
            if (indent) {
                item.addClass("ui-dropdownchecklist-indent");
            }
            item.addClass("ui-state-default");
            if (disabled) {
                item.addClass("ui-state-disabled");
            }
            label.click(function(e) {e.stopImmediatePropagation();});
            item.append(label);
            
               // active items display themselves with hover
            item.hover(
                function(e) {
                    var anItem = $(this);
                    if (!anItem.hasClass("ui-state-disabled")) { anItem.addClass("ui-state-hover"); }
                }
            ,     function(e) {
                    var anItem = $(this);
                    anItem.removeClass("ui-state-hover");
                }
            );
            // clicking on the checkbox synchronizes the source select
            checkBox.click(function(e) {
                var aCheckBox = $(this);
                e.stopImmediatePropagation();
                if (aCheckBox.hasClass("active") ) {
                    // Active checkboxes take active action
                    var callback = self.options.onItemClick;
                    if ($.isFunction(callback)) try {
                        callback.call(self,aCheckBox,sourceSelect.get(0));
                    } catch (ex) {
                        // reject the change on any error
                        aCheckBox.prop("checked",!aCheckBox.prop("checked"));
                        self._syncSelected(aCheckBox);
                        return;
                    } 
                    self._syncSelected(aCheckBox);
                    self.sourceSelect.trigger("change", 'ddcl_internal');
                    if (!self.isMultiple && options.closeRadioOnClick) {
                        self._toggleDropContainer(false);
                    }
                }
            });
            // we are interested in the focus leaving the check box
            // but we need to detect the focus leaving one check box but
            // entering another. There is no reliable way to detect who
            // received the focus on a blur, so post the blur in the future,
            // knowing we will cancel it if we capture the focus in a timely manner
            // 23Sept2010 - unfortunately, IE 7+ and Chrome like to post a blur
            //                 event to the current item with focus when the user
            //                clicks in the scroll bar. So if you have a scrollable
            //                dropdown with focus on an item, clicking in the scroll
            //                will close the drop down.
            //                I have no solution for blur processing at this time.
/*********
            var timerFunction = function(){ 
                // I had a hell of a time getting setTimeout to fire this, do not try to
                // define it within the blur function
                try { self._handleFocus(null,false,false); } catch(ex){ alert('timer failed: '+ex);}
            };
            checkBox.blur(function(e) { 
                self.blurringItem = setTimeout( timerFunction, 200 ); 
            });
            checkBox.focus(function(e) {self._cancelBlur();});
**********/    
            // check/uncheck the item on clicks on the entire item div
            item.click(function(e) {
                var anItem = $(this);
                e.stopImmediatePropagation();
                if (!anItem.hasClass("ui-state-disabled") ) {
                    // check/uncheck the underlying control
                    var aCheckBox = anItem.find("input");
                    var checked = aCheckBox.prop("checked");
                    aCheckBox.prop("checked", !checked);
                    
                    var callback = self.options.onItemClick;
                    if ($.isFunction(callback)) try {
                        callback.call(self,aCheckBox,sourceSelect.get(0));
                    } catch (ex) {
                        // reject the change on any error
                        aCheckBox.prop("checked",checked);
                        self._syncSelected(aCheckBox);
                        return;
                    } 
                    self._syncSelected(aCheckBox);
                    self.sourceSelect.trigger("change", 'ddcl_internal');
                    if (!checked && !self.isMultiple && options.closeRadioOnClick) {
                        self._toggleDropContainer(false);
                    }
                } else {
                    // retain the focus even if disabled
                    anItem.focus();
                    self._cancelBlur();
                }
            });
            // do not let the focus wander around
            item.focus(function(e) { 
                var anItem = $(this);
                e.stopImmediatePropagation();
            });
            item.keyup(function(e) {self._handleKeyboard(e);});
            return item;
        },
        _createGroupItem: function(text,disabled) {
            var self = this;
            var group = $("<div />");
            group.addClass("ui-dropdownchecklist-group ui-widget-header");
            if (disabled) {
                group.addClass("ui-state-disabled");
            }
            group.css({'white-space': "nowrap"});
            
            var label = $("<span/>");
            label.addClass("ui-dropdownchecklist-text");
            label.css( { cursor: "default" });
            label.text(text);
            group.append(label);
            
            // anything interesting when you click the group???
            group.click(function(e) {
                var aGroup= $(this);
                e.stopImmediatePropagation();
                // retain the focus even if no action is taken
                aGroup.focus();
                self._cancelBlur();
            });
            // do not let the focus wander around
            group.focus(function(e) { 
                var aGroup = $(this);
                e.stopImmediatePropagation();
            });
            return group;
        },
        _createCloseItem: function(text) {
            var self = this;
            var closeItem = $("<div />");
            closeItem.addClass("ui-state-default ui-dropdownchecklist-close ui-dropdownchecklist-item");
            closeItem.css({'white-space': 'nowrap', 'text-align': 'right'});
            
            var label = $("<span/>");
            label.addClass("ui-dropdownchecklist-text");
            label.css( { cursor: "default" });
            label.html(text);
            closeItem.append(label);
            
            // close the control on click
            closeItem.click(function(e) {
                var aGroup= $(this);
                e.stopImmediatePropagation();
                // retain the focus even if no action is taken
                aGroup.focus();
                self._toggleDropContainer( false );
            });
            closeItem.hover(
                function(e) { $(this).addClass("ui-state-hover"); }
            ,     function(e) { $(this).removeClass("ui-state-hover"); }
            );
            // do not let the focus wander around
            closeItem.focus(function(e) { 
                var aGroup = $(this);
                e.stopImmediatePropagation();
            });
            return closeItem;
        },
        // Creates the drop items and appends them to the drop container
        // Also calculates the size needed by the drop container and returns it
        _appendItems: function() {
            var self = this, config = this.options, sourceSelect = this.sourceSelect, dropWrapper = this.dropWrapper;
            var dropContainerDiv = dropWrapper.find(".ui-dropdownchecklist-dropcontainer");
            sourceSelect.children().each(function(index) { // when the select has groups
                var opt = $(this);
                if (opt.is("option")) {
                    self._appendOption(opt, dropContainerDiv, index, false, false);
                } else if (opt.is("optgroup")) {
                    var disabled = opt.prop("disabled");
                    var text = opt.attr("label");
                    if (text != "") {
                        var group = self._createGroupItem(text,disabled);
                        dropContainerDiv.append(group);
                    }
                    self._appendOptions(opt, dropContainerDiv, index, true, disabled);
                }
            });
            if ( config.explicitClose != null ) {
                var closeItem = self._createCloseItem(config.explicitClose);
                dropContainerDiv.append(closeItem);
            }
            var divWidth = dropContainerDiv.outerWidth();
            var divHeight = dropContainerDiv.outerHeight();
            return { width: divWidth, height: divHeight };
        },
        _appendOptions: function(parent, container, parentIndex, indent, forceDisabled) {
            var self = this;
            parent.children("option").each(function(index) {
                var option = $(this);
                var childIndex = (parentIndex + "." + index);
                self._appendOption(option, container, childIndex, indent, forceDisabled);
            });
        },
        _appendOption: function(option, container, index, indent, forceDisabled) {
            var self = this;
            // Note that the browsers destroy any html structure within the OPTION
            var text = option.html();
            if ( (text != null) && (text != '') ) {
                var value = option.val();
                var optCss = option.attr('style');
                var selected = option.prop("selected");
                var disabled = (forceDisabled || option.prop("disabled"));
                // Use the same tab index as the selector replacement
                var tabIndex = self.controlSelector.attr("tabindex");
                var item = self._createDropItem(index, tabIndex, value, text, optCss, selected, disabled, indent);
                container.append(item);
            }
        },
        // Synchronizes the items checked and the source select
        // When firstItemChecksAll option is active also synchronizes the checked items
        // senderCheckbox parameters is the checkbox input that generated the synchronization
        _syncSelected: function(senderCheckbox) {
            var self = this, options = this.options, sourceSelect = this.sourceSelect, dropWrapper = this.dropWrapper;
            var selectOptions = sourceSelect.get(0).options;
            var allCheckboxes = dropWrapper.find("input.active");
            if (options.firstItemChecksAll == 'exclusive') {
                if ((senderCheckbox == null) && $(selectOptions[0]).prop("selected") ) {
                    // Initialization call with first item active
                    allCheckboxes.prop("checked", false);
                    $(allCheckboxes[0]).prop("checked", true);
                } else if ((senderCheckbox != null) && (senderCheckbox.attr("index") == 0)) {
                    // Action on the first, so all other checkboxes NOT active
                    var firstIsActive = senderCheckbox.prop("checked");
                    allCheckboxes.prop("checked", false);
                    $(allCheckboxes[0]).prop("checked", firstIsActive);
                } else  {
                    // check the first checkbox if all the other checkboxes are checked
                    var allChecked = true;
                    var firstCheckbox = null;
                    allCheckboxes.each(function(index) {
                        if (index > 0) {
                            var checked = $(this).prop("checked");
                            if (!checked) { allChecked = false; }
                        } else {
                            firstCheckbox = $(this);
                        }
                    });
                    if ( firstCheckbox != null ) {
                        if ( allChecked ) {
                            // when all are checked, only the first left checked
                            allCheckboxes.prop("checked", false);
                        }
                        firstCheckbox.prop("checked", allChecked );
                    }
                }
            } else if (options.firstItemChecksAll) {
                if ((senderCheckbox == null) && $(selectOptions[0]).prop("selected") ) {
                    // Initialization call with first item active so force all to be active
                    allCheckboxes.prop("checked", true);
                } else if ((senderCheckbox != null) && (senderCheckbox.attr("index") == 0)) {
                    // Check all checkboxes if the first one is checked
                    allCheckboxes.prop("checked", senderCheckbox.prop("checked"));
                } else  {
                    // check the first checkbox if all the other checkboxes are checked
                    var allChecked = true;
                    var firstCheckbox = null;
                    allCheckboxes.each(function(index) {
                        if (index > 0) {
                            var checked = $(this).prop("checked");
                            if (!checked) { allChecked = false; }
                        } else {
                            firstCheckbox = $(this);
                        }
                    });
                    if ( firstCheckbox != null ) {
                        firstCheckbox.prop("checked", allChecked );
                    }
                }
            }
            // do the actual synch with the source select
            var empties = 0;
            allCheckboxes = dropWrapper.find("input");
            allCheckboxes.each(function(index) {
                var anOption = $(selectOptions[index + empties]);
                var optionText = anOption.html();
                if ( (optionText == null) || (optionText == '') ) {
                    empties += 1;
                    anOption = $(selectOptions[index + empties]);
                }
                anOption.prop("selected", $(this).prop("checked"));
            });
            // update the text shown in the control
            self._updateControlText();
            
            // Ensure the focus stays pointing where the user is working
            if ( senderCheckbox != null) { senderCheckbox.focus(); }
        },
        _sourceSelectChangeHandler: function(event) {
            var self = this, dropWrapper = this.dropWrapper;
            dropWrapper.find("input").val(self.sourceSelect.val());

            // update the text shown in the control
            self._updateControlText();
        },
        // Updates the text shown in the control depending on the checked (selected) items
        _updateControlText: function() {
            var self = this, sourceSelect = this.sourceSelect, options = this.options, controlWrapper = this.controlWrapper;
            var firstOption = sourceSelect.find("option:first");
            var selectOptions = sourceSelect.find("option");
            var text = self._formatText(selectOptions, options.firstItemChecksAll, firstOption);
            var controlLabel = controlWrapper.find(".ui-dropdownchecklist-text");
            controlLabel.html(text);
            // the attribute needs naked text, not html
            controlLabel.attr("title", controlLabel.text());
        },
        // Formats the text that is shown in the control
        _formatText: function(selectOptions, firstItemChecksAll, firstOption) {
            var text;
            if ( $.isFunction(this.options.textFormatFunction) ) {
                // let the callback do the formatting, but do not allow it to fail
                try {
                    text = this.options.textFormatFunction(selectOptions);
                } catch(ex) {
                    alert( 'textFormatFunction failed: ' + ex );
                }
            } else if (firstItemChecksAll && (firstOption != null) && firstOption.prop("selected")) {
                // just set the text from the first item
                text = firstOption.html();
            } else {
                // concatenate the text from the checked items
                text = "";
                selectOptions.each(function() {
                    if ($(this).prop("selected")) {
                        if ( text != "" ) { text += ", "; }
                        /* NOTE use of .html versus .text, which can screw up ampersands for IE */
                        var optCss = $(this).attr('style');
                        var tempspan = $('<span/>');
                        tempspan.html( $(this).html() );
                        if ( optCss == null ) {
                            text += tempspan.html();
                        } else {
                            tempspan.attr('style',optCss);
                            text += $("<span/>").append(tempspan).html();
                        }
                    }
                });
                if ( text == "" ) {
                    text = (this.options.emptyText != null) ? this.options.emptyText : "&nbsp;";
                }
            }
            return text;
        },
        // Shows and hides the drop container
        _toggleDropContainer: function( makeOpen ) {
            var self = this;
            // hides the last shown drop container
            var hide = function(instance) {
                if ((instance != null) && instance.dropWrapper.isOpen ){
                    instance.dropWrapper.isOpen = false;
                    $.ui.dropdownchecklist.gLastOpened = null;

                    var config = instance.options;
                    instance.dropWrapper.css({
                        top: "-33000px",
                        left: "-33000px"
                    });
                    var aControl = instance.controlSelector;
                    aControl.removeClass("ui-state-active");
                    aControl.removeClass("ui-state-hover");

                    var anIcon = instance.controlWrapper.find(".ui-icon");
                    if ( anIcon.length > 0 ) {
                        anIcon.removeClass( (config.icon.toClose != null) ? config.icon.toClose : "ui-icon-triangle-1-s");
                        anIcon.addClass( (config.icon.toOpen != null) ? config.icon.toOpen : "ui-icon-triangle-1-e");
                    }
                    $(document).unbind("click", hide);
                    
                    // keep the items out of the tab order by disabling them
                    instance.dropWrapper.find("input.active").prop("disabled",true);
                    
                    // the following blur just does not fire???  because it is hidden???  because it does not have focus???
                      //instance.sourceSelect.trigger("blur");
                      //instance.sourceSelect.triggerHandler("blur");
                      if($.isFunction(config.onComplete)) { try {
                         config.onComplete.call(instance,instance.sourceSelect.get(0));
                    } catch(ex) {
                        alert( 'callback failed: ' + ex );
                    }}
                }
            };
            // shows the given drop container instance
            var show = function(instance) {
                if ( !instance.dropWrapper.isOpen ) {
                    instance.dropWrapper.isOpen = true;
                    $.ui.dropdownchecklist.gLastOpened = instance;

                    var config = instance.options;
/**** Issue127 (and the like) to correct positioning when parent element is relative
 ****    This positioning only worked with simple, non-relative parent position
                    instance.dropWrapper.css({
                        top: instance.controlWrapper.offset().top + instance.controlWrapper.outerHeight() + "px",
                        left: instance.controlWrapper.offset().left + "px"
                    });
****/
                     if ((config.positionHow == null) || (config.positionHow == 'absolute')) {
                         /** Floats above subsequent content, but does NOT scroll */
                        instance.dropWrapper.css({
                            position: 'absolute'
                        ,   top: instance.controlWrapper.position().top + instance.controlWrapper.outerHeight() + "px"
                        ,   left: instance.controlWrapper.position().left + "px"
                        });
                    } else if (config.positionHow == 'relative') {
                        /** Scrolls with the parent but does NOT float above subsequent content */
                        instance.dropWrapper.css({
                            position: 'relative'
                        ,   top: "0px"
                        ,   left: "0px"
                        });
                    }
                    var zIndex = 0;
                    if (config.zIndex == null) {
                        var ancestorsZIndexes = instance.controlWrapper.parents().map(
                            function() {
                                var zIndex = $(this).css("z-index");
                                return isNaN(zIndex) ? 0 : zIndex; }
                            ).get();
                        var parentZIndex = Math.max.apply(Math, ancestorsZIndexes);
                        if ( parentZIndex >= 0) zIndex = parentZIndex+1;
                    } else {
                        /* Explicit set from the optins */
                        zIndex = parseInt(config.zIndex);
                    }
                    if (zIndex > 0) {
                        instance.dropWrapper.css( { 'z-index': zIndex } );
                    }

                    var aControl = instance.controlSelector;
                    aControl.addClass("ui-state-active");
                    aControl.removeClass("ui-state-hover");
                    
                    var anIcon = instance.controlWrapper.find(".ui-icon");
                    if ( anIcon.length > 0 ) {
                        anIcon.removeClass( (config.icon.toOpen != null) ? config.icon.toOpen : "ui-icon-triangle-1-e");
                        anIcon.addClass( (config.icon.toClose != null) ? config.icon.toClose : "ui-icon-triangle-1-s");
                    }
                    $(document).bind("click", function(e) {hide(instance);} );
                    
                    // insert the items back into the tab order by enabling all active ones
                    var activeItems = instance.dropWrapper.find("input.active");
                    activeItems.prop("disabled",false);
                    
                    // we want the focus on the first active input item
                    var firstActiveItem = activeItems.get(0);
                    if ( firstActiveItem != null ) {
                        firstActiveItem.focus();
                    }
                }
            };
            if ( makeOpen ) {
                hide($.ui.dropdownchecklist.gLastOpened);
                show(self);
            } else {
                hide(self);
            }
        },
        // Set the size of the control and of the drop container
        _setSize: function(dropCalculatedSize) {
            var options = this.options, dropWrapper = this.dropWrapper, controlWrapper = this.controlWrapper;

            // use the width from config options if set, otherwise set the same width as the drop container
            var controlWidth = dropCalculatedSize.width;
            if (options.width != null) {
                controlWidth = parseInt(options.width);
            } else if (options.minWidth != null) {
                var minWidth = parseInt(options.minWidth);
                // if the width is too small (usually when there are no items) set a minimum width
                if (controlWidth < minWidth) {
                    controlWidth = minWidth;
                }
            }
            var control = this.controlSelector;
            control.css({ width: controlWidth + "px" });
            
            // if we size the text, then Firefox places icons to the right properly
            // and we do not wrap on long lines
            var controlText = control.find(".ui-dropdownchecklist-text");
            var controlIcon = control.find(".ui-icon");
            if ( controlIcon != null ) {
                // Must be an inner/outer/border problem, but IE6 needs an extra bit of space,
                // otherwise you can get text pushed down into a second line when icons are active
                controlWidth -= (controlIcon.outerWidth() + 4);
                controlText.css( { width: controlWidth + "px" } );
            }
            // Account for padding, borders, etc
            controlWidth = controlWrapper.outerWidth();
            
            // the drop container height can be set from options
            var maxDropHeight = (options.maxDropHeight != null)
                                ? parseInt(options.maxDropHeight)
                                : -1;
            var dropHeight = ((maxDropHeight > 0) && (dropCalculatedSize.height > maxDropHeight))
                                ? maxDropHeight 
                                : dropCalculatedSize.height;
            // ensure the drop container is not less than the control width (would be ugly)
            var dropWidth = dropCalculatedSize.width < controlWidth ? controlWidth : dropCalculatedSize.width;

            $(dropWrapper).css({
                height: dropHeight + "px",
                width: dropWidth + "px"
            });
            dropWrapper.find(".ui-dropdownchecklist-dropcontainer").css({
                height: dropHeight + "px"
            });
        },
        // Initializes the plugin
        _init: function() {
            var self = this, options = this.options;
            if ( $.ui.dropdownchecklist.gIDCounter == null) {
                $.ui.dropdownchecklist.gIDCounter = 1;
            }
            // item blurring relies on a cancelable timer
            self.blurringItem = null;

            // sourceSelect is the select on which the plugin is applied
            var sourceSelect = self.element;
            self.initialDisplay = sourceSelect.css("display");
            sourceSelect.css("display", "none");
            self.initialMultiple = sourceSelect.prop("multiple");
            self.isMultiple = self.initialMultiple;
            if (options.forceMultiple != null) { self.isMultiple = options.forceMultiple; }
            sourceSelect.prop("multiple", true);
            self.sourceSelect = sourceSelect;

            // append the control that resembles a single selection select
            var controlWrapper = self._appendControl();
            self.controlWrapper = controlWrapper;
            self.controlSelector = controlWrapper.find(".ui-dropdownchecklist-selector");

            // create the drop container where the items are shown
            var dropWrapper = self._appendDropContainer(controlWrapper);
            self.dropWrapper = dropWrapper;

            // append the items from the source select element
            var dropCalculatedSize = self._appendItems();

            // updates the text shown in the control
            self._updateControlText(controlWrapper, dropWrapper, sourceSelect);

            // set the sizes of control and drop container
            self._setSize(dropCalculatedSize);
            
            // look for possible auto-check needed on first item
            if ( options.firstItemChecksAll ) {
                self._syncSelected(null);
            }
            // BGIFrame for IE6
            if (options.bgiframe && typeof self.dropWrapper.bgiframe == "function") {
                self.dropWrapper.bgiframe();
            }
              // listen for change events on the source select element
              // ensure we avoid processing internally triggered changes
              self.sourceSelect.change(function(event, eventName) {
                if (eventName != 'ddcl_internal') {
                    self._sourceSelectChangeHandler(event);
                }
            });
        },
        // Refresh the disable and check state from the underlying control
        _refreshOption: function(item,disabled,selected) {
            var aParent = item.parent();
            // account for enabled/disabled
            if ( disabled ) {
                item.prop("disabled",true);
                item.removeClass("active");
                item.addClass("inactive");
                aParent.addClass("ui-state-disabled");
            } else {
                item.prop("disabled",false);
                item.removeClass("inactive");
                item.addClass("active");
                aParent.removeClass("ui-state-disabled");
            }
            // adjust the checkbox state
            item.prop("checked",selected);
        },
        _refreshGroup: function(group,disabled) {
            if ( disabled ) {
                group.addClass("ui-state-disabled");
            } else {
                group.removeClass("ui-state-disabled");
            }
        },
        // External command to explicitly close the dropdown
        close: function() {
            this._toggleDropContainer(false);
        },
        // External command to refresh the ddcl from the underlying selector
        refresh: function() {
            var self = this, sourceSelect = this.sourceSelect, dropWrapper = this.dropWrapper;
            
            var allCheckBoxes = dropWrapper.find("input");
            var allGroups = dropWrapper.find(".ui-dropdownchecklist-group");
            
            var groupCount = 0;
            var optionCount = 0;
            sourceSelect.children().each(function(index) {
                var opt = $(this);
                var disabled = opt.prop("disabled");
                if (opt.is("option")) {
                    var selected = opt.prop("selected");
                    var anItem = $(allCheckBoxes[optionCount]);
                    self._refreshOption(anItem, disabled, selected);
                    optionCount += 1;
                } else if (opt.is("optgroup")) {
                    var text = opt.attr("label");
                    if (text != "") {
                        var aGroup = $(allGroups[groupCount]);
                        self._refreshGroup(aGroup, disabled);
                        groupCount += 1;
                    }
                    opt.children("option").each(function() {
                        var subopt = $(this);
                        var subdisabled = (disabled || subopt.prop("disabled"));
                        var selected = subopt.prop("selected");
                        var subItem = $(allCheckBoxes[optionCount]);
                        self._refreshOption(subItem, subdisabled, selected );
                        optionCount += 1;
                    });
                }
            });
            // sync will handle firstItemChecksAll and updateControlText
            self._syncSelected(null);
        },
        // External command to enable the ddcl control
        enable: function() {
            this.controlSelector.removeClass("ui-state-disabled");
            this.disabled = false;
        },
        // External command to disable the ddcl control
        disable: function() {
            this.controlSelector.addClass("ui-state-disabled");
            this.disabled = true;
        },
        // External command to destroy all traces of the ddcl control
        destroy: function() {
            $.Widget.prototype.destroy.apply(this, arguments);
            this.sourceSelect.css("display", this.initialDisplay);
            this.sourceSelect.prop("multiple", this.initialMultiple);
            this.controlWrapper.unbind().remove();
            this.dropWrapper.remove();
        }
    });

    $.extend($.ui.dropdownchecklist, {
        defaults: {
            width: null
        ,   maxDropHeight: null
        ,   firstItemChecksAll: false
        ,   closeRadioOnClick: false
        ,   minWidth: 50
        ,   positionHow: 'absolute'
        ,   bgiframe: false
        ,    explicitClose: null
        }
    });

})(jQuery);

/*
 * jQuery dropdown: A simple dropdown plugin
 *
 * Inspired by Bootstrap: http://twitter.github.com/bootstrap/javascript.html#dropdowns
 *
 * Copyright 2013 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 *
 * Dual licensed under the MIT / GPL Version 2 licenses
 *
*/
if(jQuery) (function($) {
    
    $.extend($.fn, {
        dropdown: function(method, data) {
            
            switch( method ) {
                case 'hide':
                    hide();
                    return $(this);
                case 'attach':
                    return $(this).attr('data-dropdown', data);
                case 'detach':
                    hide();
                    return $(this).removeAttr('data-dropdown');
                case 'disable':
                    return $(this).addClass('dropdown-disabled');
                case 'enable':
                    hide();
                    return $(this).removeClass('dropdown-disabled');
            }
            
        }
    });
    
    function show(event) {
        
        var trigger = $(this),
            dropdown = $(trigger.attr('data-dropdown')),
            isOpen = trigger.hasClass('dropdown-open');
        
        // In some cases we don't want to show it
        if( $(event.target).hasClass('dropdown-ignore') ) return;
        
        event.preventDefault();
        event.stopPropagation();
        hide();
        
        if( isOpen || trigger.hasClass('dropdown-disabled') ) return;
        
        // Show it
        trigger.addClass('dropdown-open');
        dropdown
            .data('dropdown-trigger', trigger)
            .show();
            
        // Position it
        position();
        
        // Trigger the show callback
        dropdown
            .trigger('show', {
                dropdown: dropdown,
                trigger: trigger
            });
        
    }
    
    function hide(event) {
        
        // In some cases we don't hide them
        var targetGroup = event ? $(event.target).parents().addBack() : null;
        
        // Are we clicking anywhere in a dropdown?
        if( targetGroup && targetGroup.is('.dropdown') ) {
            // Is it a dropdown menu?
            if( targetGroup.is('.dropdown-menu') ) {
                // Did we click on an option? If so close it.
                if( !targetGroup.is('A') ) return;
            } else {
                // Nope, it's a panel. Leave it open.
                return;
            }
        }
        
        // Hide any dropdown that may be showing
        $(document).find('.dropdown:visible').each( function() {
            var dropdown = $(this);
            dropdown
                .hide()
                .removeData('dropdown-trigger')
                .trigger('hide', { dropdown: dropdown });
        });
        
        // Remove all dropdown-open classes
        $(document).find('.dropdown-open').removeClass('dropdown-open');
        
    }
    
    function position() {
        
        var dropdown = $('.dropdown:visible').eq(0),
            trigger = dropdown.data('dropdown-trigger'),
            hOffset = trigger ? parseInt(trigger.attr('data-horizontal-offset') || 0, 10) : null,
            vOffset = trigger ? parseInt(trigger.attr('data-vertical-offset') || 0, 10) : null;
        
        if( dropdown.length === 0 || !trigger ) return;
        
        // Position the dropdown relative-to-parent...
        if( dropdown.hasClass('dropdown-relative') ) {
            dropdown.css({
                left: dropdown.hasClass('dropdown-anchor-right') ?
                    trigger.position().left - (dropdown.outerWidth(true) - trigger.outerWidth(true)) - parseInt(trigger.css('margin-right')) + hOffset :
                    trigger.position().left + parseInt(trigger.css('margin-left')) + hOffset,
                top: trigger.position().top + trigger.outerHeight(true) - parseInt(trigger.css('margin-top')) + vOffset
            });
        } else {
            // ...or relative to document
            //dropdown.css({
                //left: dropdown.hasClass('dropdown-anchor-right') ? 
                    //trigger.offset().left - (dropdown.outerWidth() - trigger.outerWidth()) + hOffset : trigger.offset().left + hOffset,
                //top: trigger.offset().top + trigger.outerHeight() + vOffset
            //});
        }
    }
    
    $(document).on('click.dropdown', '[data-dropdown]', show);
    $(document).on('click.dropdown', hide);
    $(window).on('resize', position);
    
})(jQuery);


/*!
 * jQuery Migrate - v1.2.1 - 2013-05-08
 * https://github.com/jquery/jquery-migrate
 * Copyright 2005, 2013 jQuery Foundation, Inc. and other contributors; Licensed MIT
 */
(function( jQuery, window, undefined ) {
// See http://bugs.jquery.com/ticket/13335
// "use strict";


var warnedAbout = {};

// List of warnings already given; public read only
jQuery.migrateWarnings = [];

// Set to true to prevent console output; migrateWarnings still maintained
// jQuery.migrateMute = false;

// Show a message on the console so devs know we're active
if ( !jQuery.migrateMute && window.console && window.console.log ) {
    window.console.log("JQMIGRATE: Logging is active");
}

// Set to false to disable traces that appear with warnings
if ( jQuery.migrateTrace === undefined ) {
    jQuery.migrateTrace = true;
}

// Forget any warnings we've already given; public
jQuery.migrateReset = function() {
    warnedAbout = {};
    jQuery.migrateWarnings.length = 0;
};

function migrateWarn( msg) {
    var console = window.console;
    if ( !warnedAbout[ msg ] ) {
        warnedAbout[ msg ] = true;
        jQuery.migrateWarnings.push( msg );
        if ( console && console.warn && !jQuery.migrateMute ) {
            console.warn( "JQMIGRATE: " + msg );
            if ( jQuery.migrateTrace && console.trace ) {
                console.trace();
            }
        }
    }
}

function migrateWarnProp( obj, prop, value, msg ) {
    if ( Object.defineProperty ) {
        // On ES5 browsers (non-oldIE), warn if the code tries to get prop;
        // allow property to be overwritten in case some other plugin wants it
        try {
            Object.defineProperty( obj, prop, {
                configurable: true,
                enumerable: true,
                get: function() {
                    migrateWarn( msg );
                    return value;
                },
                set: function( newValue ) {
                    migrateWarn( msg );
                    value = newValue;
                }
            });
            return;
        } catch( err ) {
            // IE8 is a dope about Object.defineProperty, can't warn there
        }
    }

    // Non-ES5 (or broken) browser; just set the property
    jQuery._definePropertyBroken = true;
    obj[ prop ] = value;
}

if ( document.compatMode === "BackCompat" ) {
    // jQuery has never supported or tested Quirks Mode
    migrateWarn( "jQuery is not compatible with Quirks Mode" );
}


var attrFn = jQuery( "<input/>", { size: 1 } ).attr("size") && jQuery.attrFn,
    oldAttr = jQuery.attr,
    valueAttrGet = jQuery.attrHooks.value && jQuery.attrHooks.value.get ||
        function() { return null; },
    valueAttrSet = jQuery.attrHooks.value && jQuery.attrHooks.value.set ||
        function() { return undefined; },
    rnoType = /^(?:input|button)$/i,
    rnoAttrNodeType = /^[238]$/,
    rboolean = /^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,
    ruseDefault = /^(?:checked|selected)$/i;

// jQuery.attrFn
migrateWarnProp( jQuery, "attrFn", attrFn || {}, "jQuery.attrFn is deprecated" );

jQuery.attr = function( elem, name, value, pass ) {
    var lowerName = name.toLowerCase(),
        nType = elem && elem.nodeType;

    if ( pass ) {
        // Since pass is used internally, we only warn for new jQuery
        // versions where there isn't a pass arg in the formal params
        if ( oldAttr.length < 4 ) {
            migrateWarn("jQuery.fn.attr( props, pass ) is deprecated");
        }
        if ( elem && !rnoAttrNodeType.test( nType ) &&
            (attrFn ? name in attrFn : jQuery.isFunction(jQuery.fn[name])) ) {
            return jQuery( elem )[ name ]( value );
        }
    }

    // Warn if user tries to set `type`, since it breaks on IE 6/7/8; by checking
    // for disconnected elements we don't warn on $( "<button>", { type: "button" } ).
    if ( name === "type" && value !== undefined && rnoType.test( elem.nodeName ) && elem.parentNode ) {
        migrateWarn("Can't change the 'type' of an input or button in IE 6/7/8");
    }

    // Restore boolHook for boolean property/attribute synchronization
    if ( !jQuery.attrHooks[ lowerName ] && rboolean.test( lowerName ) ) {
        jQuery.attrHooks[ lowerName ] = {
            get: function( elem, name ) {
                // Align boolean attributes with corresponding properties
                // Fall back to attribute presence where some booleans are not supported
                var attrNode,
                    property = jQuery.prop( elem, name );
                return property === true || typeof property !== "boolean" &&
                    ( attrNode = elem.getAttributeNode(name) ) && attrNode.nodeValue !== false ?

                    name.toLowerCase() :
                    undefined;
            },
            set: function( elem, value, name ) {
                var propName;
                if ( value === false ) {
                    // Remove boolean attributes when set to false
                    jQuery.removeAttr( elem, name );
                } else {
                    // value is true since we know at this point it's type boolean and not false
                    // Set boolean attributes to the same name and set the DOM property
                    propName = jQuery.propFix[ name ] || name;
                    if ( propName in elem ) {
                        // Only set the IDL specifically if it already exists on the element
                        elem[ propName ] = true;
                    }

                    elem.setAttribute( name, name.toLowerCase() );
                }
                return name;
            }
        };

        // Warn only for attributes that can remain distinct from their properties post-1.9
        if ( ruseDefault.test( lowerName ) ) {
            migrateWarn( "jQuery.fn.attr('" + lowerName + "') may use property instead of attribute" );
        }
    }

    return oldAttr.call( jQuery, elem, name, value );
};

// attrHooks: value
jQuery.attrHooks.value = {
    get: function( elem, name ) {
        var nodeName = ( elem.nodeName || "" ).toLowerCase();
        if ( nodeName === "button" ) {
            return valueAttrGet.apply( this, arguments );
        }
        if ( nodeName !== "input" && nodeName !== "option" ) {
            migrateWarn("jQuery.fn.attr('value') no longer gets properties");
        }
        return name in elem ?
            elem.value :
            null;
    },
    set: function( elem, value ) {
        var nodeName = ( elem.nodeName || "" ).toLowerCase();
        if ( nodeName === "button" ) {
            return valueAttrSet.apply( this, arguments );
        }
        if ( nodeName !== "input" && nodeName !== "option" ) {
            migrateWarn("jQuery.fn.attr('value', val) no longer sets properties");
        }
        // Does not return so that setAttribute is also used
        elem.value = value;
    }
};


var matched, browser,
    oldInit = jQuery.fn.init,
    oldParseJSON = jQuery.parseJSON,
    // Note: XSS check is done below after string is trimmed
    rquickExpr = /^([^<]*)(<[\w\W]+>)([^>]*)$/;

// $(html) "looks like html" rule change
jQuery.fn.init = function( selector, context, rootjQuery ) {
    var match;

    if ( selector && typeof selector === "string" && !jQuery.isPlainObject( context ) &&
            (match = rquickExpr.exec( jQuery.trim( selector ) )) && match[ 0 ] ) {
        // This is an HTML string according to the "old" rules; is it still?
        if ( selector.charAt( 0 ) !== "<" ) {
            migrateWarn("$(html) HTML strings must start with '<' character");
        }
        if ( match[ 3 ] ) {
            migrateWarn("$(html) HTML text after last tag is ignored");
        }
        // Consistently reject any HTML-like string starting with a hash (#9521)
        // Note that this may break jQuery 1.6.x code that otherwise would work.
        if ( match[ 0 ].charAt( 0 ) === "#" ) {
            migrateWarn("HTML string cannot start with a '#' character");
            jQuery.error("JQMIGRATE: Invalid selector string (XSS)");
        }
        // Now process using loose rules; let pre-1.8 play too
        if ( context && context.context ) {
            // jQuery object as context; parseHTML expects a DOM object
            context = context.context;
        }
        if ( jQuery.parseHTML ) {
            return oldInit.call( this, jQuery.parseHTML( match[ 2 ], context, true ),
                    context, rootjQuery );
        }
    }
    return oldInit.apply( this, arguments );
};
jQuery.fn.init.prototype = jQuery.fn;

// Let $.parseJSON(falsy_value) return null
jQuery.parseJSON = function( json ) {
    if ( !json && json !== null ) {
        migrateWarn("jQuery.parseJSON requires a valid JSON string");
        return null;
    }
    return oldParseJSON.apply( this, arguments );
};

jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

// Don't clobber any existing jQuery.browser in case it's different
if ( !jQuery.browser ) {
    matched = jQuery.uaMatch( navigator.userAgent );
    browser = {};

    if ( matched.browser ) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }

    // Chrome is Webkit, but Webkit is also Safari.
    if ( browser.chrome ) {
        browser.webkit = true;
    } else if ( browser.webkit ) {
        browser.safari = true;
    }

    jQuery.browser = browser;
}

// Warn if the code tries to get jQuery.browser
migrateWarnProp( jQuery, "browser", jQuery.browser, "jQuery.browser is deprecated" );

jQuery.sub = function() {
    function jQuerySub( selector, context ) {
        return new jQuerySub.fn.init( selector, context );
    }
    jQuery.extend( true, jQuerySub, this );
    jQuerySub.superclass = this;
    jQuerySub.fn = jQuerySub.prototype = this();
    jQuerySub.fn.constructor = jQuerySub;
    jQuerySub.sub = this.sub;
    jQuerySub.fn.init = function init( selector, context ) {
        if ( context && context instanceof jQuery && !(context instanceof jQuerySub) ) {
            context = jQuerySub( context );
        }

        return jQuery.fn.init.call( this, selector, context, rootjQuerySub );
    };
    jQuerySub.fn.init.prototype = jQuerySub.fn;
    var rootjQuerySub = jQuerySub(document);
    migrateWarn( "jQuery.sub() is deprecated" );
    return jQuerySub;
};


// Ensure that $.ajax gets the new parseJSON defined in core.js
jQuery.ajaxSetup({
    converters: {
        "text json": jQuery.parseJSON
    }
});


var oldFnData = jQuery.fn.data;

jQuery.fn.data = function( name ) {
    var ret, evt,
        elem = this[0];

    // Handles 1.7 which has this behavior and 1.8 which doesn't
    if ( elem && name === "events" && arguments.length === 1 ) {
        ret = jQuery.data( elem, name );
        evt = jQuery._data( elem, name );
        if ( ( ret === undefined || ret === evt ) && evt !== undefined ) {
            migrateWarn("Use of jQuery.fn.data('events') is deprecated");
            return evt;
        }
    }
    return oldFnData.apply( this, arguments );
};


var rscriptType = /\/(java|ecma)script/i,
    oldSelf = jQuery.fn.andSelf || jQuery.fn.addBack;

jQuery.fn.andSelf = function() {
    migrateWarn("jQuery.fn.andSelf() replaced by jQuery.fn.addBack()");
    return oldSelf.apply( this, arguments );
};

// Since jQuery.clean is used internally on older versions, we only shim if it's missing
if ( !jQuery.clean ) {
    jQuery.clean = function( elems, context, fragment, scripts ) {
        // Set context per 1.8 logic
        context = context || document;
        context = !context.nodeType && context[0] || context;
        context = context.ownerDocument || context;

        migrateWarn("jQuery.clean() is deprecated");

        var i, elem, handleScript, jsTags,
            ret = [];

        jQuery.merge( ret, jQuery.buildFragment( elems, context ).childNodes );

        // Complex logic lifted directly from jQuery 1.8
        if ( fragment ) {
            // Special handling of each script element
            handleScript = function( elem ) {
                // Check if we consider it executable
                if ( !elem.type || rscriptType.test( elem.type ) ) {
                    // Detach the script and store it in the scripts array (if provided) or the fragment
                    // Return truthy to indicate that it has been handled
                    return scripts ?
                        scripts.push( elem.parentNode ? elem.parentNode.removeChild( elem ) : elem ) :
                        fragment.appendChild( elem );
                }
            };

            for ( i = 0; (elem = ret[i]) != null; i++ ) {
                // Check if we're done after handling an executable script
                if ( !( jQuery.nodeName( elem, "script" ) && handleScript( elem ) ) ) {
                    // Append to fragment and handle embedded scripts
                    fragment.appendChild( elem );
                    if ( typeof elem.getElementsByTagName !== "undefined" ) {
                        // handleScript alters the DOM, so use jQuery.merge to ensure snapshot iteration
                        jsTags = jQuery.grep( jQuery.merge( [], elem.getElementsByTagName("script") ), handleScript );

                        // Splice the scripts into ret after their former ancestor and advance our index beyond them
                        ret.splice.apply( ret, [i + 1, 0].concat( jsTags ) );
                        i += jsTags.length;
                    }
                }
            }
        }

        return ret;
    };
}

var eventAdd = jQuery.event.add,
    eventRemove = jQuery.event.remove,
    eventTrigger = jQuery.event.trigger,
    oldToggle = jQuery.fn.toggle,
    oldLive = jQuery.fn.live,
    oldDie = jQuery.fn.die,
    ajaxEvents = "ajaxStart|ajaxStop|ajaxSend|ajaxComplete|ajaxError|ajaxSuccess",
    rajaxEvent = new RegExp( "\\b(?:" + ajaxEvents + ")\\b" ),
    rhoverHack = /(?:^|\s)hover(\.\S+|)\b/,
    hoverHack = function( events ) {
        if ( typeof( events ) !== "string" || jQuery.event.special.hover ) {
            return events;
        }
        if ( rhoverHack.test( events ) ) {
            migrateWarn("'hover' pseudo-event is deprecated, use 'mouseenter mouseleave'");
        }
        return events && events.replace( rhoverHack, "mouseenter$1 mouseleave$1" );
    };

// Event props removed in 1.9, put them back if needed; no practical way to warn them
if ( jQuery.event.props && jQuery.event.props[ 0 ] !== "attrChange" ) {
    jQuery.event.props.unshift( "attrChange", "attrName", "relatedNode", "srcElement" );
}

// Undocumented jQuery.event.handle was "deprecated" in jQuery 1.7
if ( jQuery.event.dispatch ) {
    migrateWarnProp( jQuery.event, "handle", jQuery.event.dispatch, "jQuery.event.handle is undocumented and deprecated" );
}

// Support for 'hover' pseudo-event and ajax event warnings
jQuery.event.add = function( elem, types, handler, data, selector ){
    if ( elem !== document && rajaxEvent.test( types ) ) {
        migrateWarn( "AJAX events should be attached to document: " + types );
    }
    eventAdd.call( this, elem, hoverHack( types || "" ), handler, data, selector );
};
jQuery.event.remove = function( elem, types, handler, selector, mappedTypes ){
    eventRemove.call( this, elem, hoverHack( types ) || "", handler, selector, mappedTypes );
};

jQuery.fn.error = function() {
    var args = Array.prototype.slice.call( arguments, 0);
    migrateWarn("jQuery.fn.error() is deprecated");
    args.splice( 0, 0, "error" );
    if ( arguments.length ) {
        return this.bind.apply( this, args );
    }
    // error event should not bubble to window, although it does pre-1.7
    this.triggerHandler.apply( this, args );
    return this;
};

jQuery.fn.toggle = function( fn, fn2 ) {

    // Don't mess with animation or css toggles
    if ( !jQuery.isFunction( fn ) || !jQuery.isFunction( fn2 ) ) {
        return oldToggle.apply( this, arguments );
    }
    migrateWarn("jQuery.fn.toggle(handler, handler...) is deprecated");

    // Save reference to arguments for access in closure
    var args = arguments,
        guid = fn.guid || jQuery.guid++,
        i = 0,
        toggler = function( event ) {
            // Figure out which function to execute
            var lastToggle = ( jQuery._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
            jQuery._data( this, "lastToggle" + fn.guid, lastToggle + 1 );

            // Make sure that clicks stop
            event.preventDefault();

            // and execute the function
            return args[ lastToggle ].apply( this, arguments ) || false;
        };

    // link all the functions, so any of them can unbind this click handler
    toggler.guid = guid;
    while ( i < args.length ) {
        args[ i++ ].guid = guid;
    }

    return this.click( toggler );
};

jQuery.fn.live = function( types, data, fn ) {
    migrateWarn("jQuery.fn.live() is deprecated");
    if ( oldLive ) {
        return oldLive.apply( this, arguments );
    }
    jQuery( this.context ).on( types, this.selector, data, fn );
    return this;
};

jQuery.fn.die = function( types, fn ) {
    migrateWarn("jQuery.fn.die() is deprecated");
    if ( oldDie ) {
        return oldDie.apply( this, arguments );
    }
    jQuery( this.context ).off( types, this.selector || "**", fn );
    return this;
};

// Turn global events into document-triggered events
jQuery.event.trigger = function( event, data, elem, onlyHandlers  ){
    if ( !elem && !rajaxEvent.test( event ) ) {
        migrateWarn( "Global events are undocumented and deprecated" );
    }
    return eventTrigger.call( this,  event, data, elem || document, onlyHandlers  );
};
jQuery.each( ajaxEvents.split("|"),
    function( _, name ) {
        jQuery.event.special[ name ] = {
            setup: function() {
                var elem = this;

                // The document needs no shimming; must be !== for oldIE
                if ( elem !== document ) {
                    jQuery.event.add( document, name + "." + jQuery.guid, function() {
                        jQuery.event.trigger( name, null, elem, true );
                    });
                    jQuery._data( this, name, jQuery.guid++ );
                }
                return false;
            },
            teardown: function() {
                if ( this !== document ) {
                    jQuery.event.remove( document, name + "." + jQuery._data( this, name ) );
                }
                return false;
            }
        };
    }
);


})( jQuery, window );

/*
 * jQuery Impromptu
 * By: Trent Richardson [http://trentrichardson.com]
 * Version 3.1
 * Last Modified: 3/30/2010
 * 
 * Copyright 2010 Trent Richardson
 * Dual licensed under the MIT and GPL licenses.
 * http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
 * http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
 * 
 */
 
(function($) {
    $.prompt = function(message, options) {
        options = $.extend({},$.prompt.defaults,options);
        $.prompt.currentPrefix = options.prefix;

        var ie6        = ($.browser.msie && $.browser.version < 7);
        var $body    = $(document.body);
        var $window    = $(window);
        
        options.classes = $.trim(options.classes);
        if(options.classes != '')
            options.classes = ' '+ options.classes;
            
        //build the box and fade
        var msgbox = '<div class="'+ options.prefix +'box'+ options.classes +'" id="'+ options.prefix +'box">';
        if(options.useiframe && (($('object, applet').length > 0) || ie6)) {
            msgbox += '<iframe src="javascript:false;" style="display:block;position:absolute;z-index:-1;" class="'+ options.prefix +'fade" id="'+ options.prefix +'fade"></iframe>';
        } else {
            if(ie6) {
                $('select').css('visibility','hidden');
            }
            msgbox +='<div class="'+ options.prefix +'fade" id="'+ options.prefix +'fade"></div>';
        }
        msgbox += '<div class="'+ options.prefix +'" id="'+ options.prefix +'"><div class="'+ options.prefix +'container"><div class="';
        msgbox += options.prefix +'close">X</div><div id="'+ options.prefix +'states"></div>';
        msgbox += '</div></div></div>';

        var $jqib    = $(msgbox).appendTo($body);
        var $jqi    = $jqib.children('#'+ options.prefix);
        var $jqif    = $jqib.children('#'+ options.prefix +'fade');

        //if a string was passed, convert to a single state
        if(message.constructor == String){
            message = {
                state0: {
                    html: message,
                     buttons: options.buttons,
                     focus: options.focus,
                     submit: options.submit
                 }
             };
        }

        //build the states
        var states = "";

        $.each(message,function(statename,stateobj){
            stateobj = $.extend({},$.prompt.defaults.state,stateobj);
            message[statename] = stateobj;

            states += '<div id="'+ options.prefix +'_state_'+ statename +'" class="'+ options.prefix + '_state" style="display:none;"><div class="'+ options.prefix +'message">' + stateobj.html +'</div><div class="'+ options.prefix +'buttons">';
            $.each(stateobj.buttons, function(k, v){
                if(typeof v == 'object')
                    states += '<button name="' + options.prefix + '_' + statename + '_button' + v.title.replace(/[^a-z0-9]+/gi,'') + '" id="' + options.prefix + '_' + statename + '_button' + v.title.replace(/[^a-z0-9]+/gi,'') + '" value="' + v.value + '">' + v.title + '</button>';
                else states += '<button name="' + options.prefix + '_' + statename + '_button' + k + '" id="' + options.prefix +    '_' + statename + '_button' + k + '" value="' + v + '">' + k + '</button>';
            });
            states += '</div></div>';
        });

        //insert the states...
        $jqi.find('#'+ options.prefix +'states').html(states).children('.'+ options.prefix +'_state:first').css('display','block');
        $jqi.find('.'+ options.prefix +'buttons:empty').css('display','none');
        
        //Events
        $.each(message,function(statename,stateobj){
            var $state = $jqi.find('#'+ options.prefix +'_state_'+ statename);

            $state.children('.'+ options.prefix +'buttons').children('button').click(function(){
                var msg = $state.children('.'+ options.prefix +'message');
                var clicked = stateobj.buttons[$(this).text()];
                if(clicked == undefined){
                    for(var i in stateobj.buttons)
                        if(stateobj.buttons[i].title == $(this).text())
                            clicked = stateobj.buttons[i].value;
                }
                
                if(typeof clicked == 'object')
                    clicked = clicked.value;
                var forminputs = {};

                //collect all form element values from all states
                $.each($jqi.find('#'+ options.prefix +'states :input').serializeArray(),function(i,obj){
                    if (forminputs[obj.name] === undefined) {
                        forminputs[obj.name] = obj.value;
                    } else if (typeof forminputs[obj.name] == Array || typeof forminputs[obj.name] == 'object') {
                        forminputs[obj.name].push(obj.value);
                    } else {
                        forminputs[obj.name] = [forminputs[obj.name],obj.value];    
                    } 
                });

                var close = stateobj.submit(clicked,msg,forminputs);
                if(close === undefined || close) {
                    removePrompt(true,clicked,msg,forminputs);
                }
            });
            $state.find('.'+ options.prefix +'buttons button:eq('+ stateobj.focus +')').addClass(options.prefix +'defaultbutton');

        });

        var ie6scroll = function(){
            $jqib.css({ top: $window.scrollTop() });
        };

        var fadeClicked = function(){
            if(options.persistent){
                var i = 0;
                $jqib.addClass(options.prefix +'warning');
                var intervalid = setInterval(function(){
                    $jqib.toggleClass(options.prefix +'warning');
                    if(i++ > 1){
                        clearInterval(intervalid);
                        $jqib.removeClass(options.prefix +'warning');
                    }
                }, 100);
            }
            else {
                removePrompt();
            }
        };
        
        var keyPressEventHandler = function(e){
            var key = (window.event) ? event.keyCode : e.keyCode; // MSIE or Firefox?
            
            //escape key closes
            if(key==27) {
                fadeClicked();    
            }
            
            //constrain tabs
            if (key == 9){
                var $inputels = $(':input:enabled:visible',$jqib);
                var fwd = !e.shiftKey && e.target == $inputels[$inputels.length-1];
                var back = e.shiftKey && e.target == $inputels[0];
                if (fwd || back) {
                setTimeout(function(){ 
                    if (!$inputels)
                        return;
                    var el = $inputels[back===true ? $inputels.length-1 : 0];

                    if (el)
                        el.focus();                        
                },10);
                return false;
                }
            }
        };
        
        var positionPrompt = function(){
            $jqib.css({
                position: (ie6) ? "absolute" : "fixed",
                height: $window.height(),
                width: "100%",
                top: (ie6)? $window.scrollTop() : 0,
                left: 0,
                right: 0,
                bottom: 0
            });
            $jqif.css({
                position: "absolute",
                height: $window.height(),
                width: "100%",
                top: 0,
                left: 0,
                right: 0,
                bottom: 0
            });
            $jqi.css({
                position: "absolute",
                top: options.top,
                left: "50%",
                marginLeft: (($jqi.outerWidth()/2)*-1)
            });
        };

        var stylePrompt = function(){
            $jqif.css({
                zIndex: options.zIndex,
                display: "none",
                opacity: options.opacity
            });
            $jqi.css({
                zIndex: options.zIndex+1,
                display: "none"
            });
            $jqib.css({
                zIndex: options.zIndex
            });
        };

        var removePrompt = function(callCallback, clicked, msg, formvals){
            $jqi.remove();
            //ie6, remove the scroll event
            if(ie6) {
                $body.unbind('scroll',ie6scroll);
            }
            $window.unbind('resize',positionPrompt);
            $jqif.fadeOut(options.overlayspeed,function(){
                $jqif.unbind('click',fadeClicked);
                $jqif.remove();
                if(callCallback) {
                    options.callback(clicked,msg,formvals);
                }
                $jqib.unbind('keypress',keyPressEventHandler);
                $jqib.remove();
                if(ie6 && !options.useiframe) {
                    $('select').css('visibility','visible');
                }
            });
        };

        positionPrompt();
        stylePrompt();
        
        //ie6, add a scroll event to fix position:fixed
        if(ie6) {
            $window.scroll(ie6scroll);
        }
        $jqif.click(fadeClicked);
        $window.resize(positionPrompt);
        $jqib.bind("keydown keypress",keyPressEventHandler);
        $jqi.find('.'+ options.prefix +'close').click(removePrompt);

        //Show it
        $jqif.fadeIn(options.overlayspeed);
        $jqi[options.show](options.promptspeed,options.loaded);
        $jqi.find('#'+ options.prefix +'states .'+ options.prefix +'_state:first .'+ options.prefix +'defaultbutton').focus();
        
        if(options.timeout > 0)
            setTimeout($.prompt.close,options.timeout);

        return $jqib;
    };
    
    $.prompt.defaults = {
        prefix:'jqi',
        classes: '',
        buttons: {
            Ok: true
        },
         loaded: function(){

         },
          submit: function(){
              return true;
        },
         callback: function(){

         },
        opacity: 0.6,
         zIndex: 999,
          overlayspeed: 'slow',
           promptspeed: 'fast',
           show: 'fadeIn',
           focus: 0,
           useiframe: false,
         top: "15%",
          persistent: true,
          timeout: 0,
          state: {
            html: '',
             buttons: {
                 Ok: true
             },
              focus: 0,
               submit: function(){
                   return true;
           }
          }
    };
    
    $.prompt.currentPrefix = $.prompt.defaults.prefix;

    $.prompt.setDefaults = function(o) {
        $.prompt.defaults = $.extend({}, $.prompt.defaults, o);
    };
    
    $.prompt.setStateDefaults = function(o) {
        $.prompt.defaults.state = $.extend({}, $.prompt.defaults.state, o);
    };
    
    $.prompt.getStateContent = function(state) {
        return $('#'+ $.prompt.currentPrefix +'_state_'+ state);
    };
    
    $.prompt.getCurrentState = function() {
        return $('.'+ $.prompt.currentPrefix +'_state:visible');
    };
    
    $.prompt.getCurrentStateName = function() {
        var stateid = $.prompt.getCurrentState().attr('id');
        
        return stateid.replace($.prompt.currentPrefix +'_state_','');
    };
    
    $.prompt.goToState = function(state, callback) {
        $('.'+ $.prompt.currentPrefix +'_state').slideUp('slow');
        $('#'+ $.prompt.currentPrefix +'_state_'+ state).slideDown('slow',function(){
            $(this).find('.'+ $.prompt.currentPrefix +'defaultbutton').focus();
            if (typeof callback == 'function')
                callback();
        });
    };
    
    $.prompt.nextState = function(callback) {
        var $next = $('.'+ $.prompt.currentPrefix +'_state:visible').next();

        $('.'+ $.prompt.currentPrefix +'_state').slideUp('slow');
        
        $next.slideDown('slow',function(){
            $next.find('.'+ $.prompt.currentPrefix +'defaultbutton').focus();
            if (typeof callback == 'function')
                callback();
        });
    };
    
    $.prompt.prevState = function(callback) {
        var $next = $('.'+ $.prompt.currentPrefix +'_state:visible').prev();

        $('.'+ $.prompt.currentPrefix +'_state').slideUp('slow');
        
        $next.slideDown('slow',function(){
            $next.find('.'+ $.prompt.currentPrefix +'defaultbutton').focus();
            if (typeof callback == 'function')
                callback();
        });
    };
    
    $.prompt.close = function() {
        $('#'+ $.prompt.currentPrefix +'box').fadeOut('fast',function(){
                $(this).remove();
        });
    };
    
    $.fn.prompt = function(options){
        if(options == undefined) 
            options = {};
        if(options.withDataAndEvents == undefined)
            options.withDataAndEvents = false;
            
        $.prompt($(this).clone(options.withDataAndEvents).html(),options);
    }
    
})(jQuery);

/*-------------------------------------------------------------------------------
    A Better jQuery Tooltip
    Version 1.0
    By Jon Cazier
    jon@3nhanced.com
    01.22.08
-------------------------------------------------------------------------------*/

j.fn.betterTooltip = function(options){
    
    /* Setup the options for the tooltip that can be 
       accessed from outside the plugin              */
    var defaults = {
        speed: 200,
        delay: 300
    };
    
    var options = j.extend(defaults, options);
    
    /* Create a function that builds the tooltip 
       markup. Then, prepend the tooltip to the body */
    getTip = function() {
        var tTip = 
            "<div class='tip'>" +
                "<div class='tipMid'>"    +
                "</div>" +
                "<div class='tipBtm'></div>" +
            "</div>";
        return tTip;
    }
    j("body").prepend(getTip());
    
    /* Give each item with the class associated with 
       the plugin the ability to call the tooltip    */
    j(this).each(function(){
        
        var $this = j(this);
        var tip = j('.tip');
        var tipInner = j('.tip .tipMid');
        
        var tTitle = (this.title);
        this.title = "";
        
        var offset = j(this).offset();
        var tLeft = offset.left;
        var tTop = offset.top;
        var tWidth = $this.width();
        var tHeight = $this.height();
        
        /* Mouse over and out functions*/
        $this.hover(
            function() {
                tipInner.html(tTitle);
                setTip(tTop, tLeft);
                setTimer();
            }, 
            function() {
                stopTimer();
                tip.hide();
            }
        );           
        
        /* Delay the fade-in animation of the tooltip */
        setTimer = function() {
            $this.showTipTimer = setInterval("showTip()", defaults.delay);
        }
        
        stopTimer = function() {
            clearInterval($this.showTipTimer);
        }
        
        /* Position the tooltip relative to the class 
           associated with the tooltip                */
        setTip = function(top, left){
            var topOffset = tip.height();
            var xTip = (left-30)+"px";
            var yTip = (top-topOffset-60)+"px";
            tip.css({'top' : yTip, 'left' : xTip});
        }
        
        /* This function stops the timer and creates the
           fade-in animation                          */
        showTip = function(){
            stopTimer();
            tip.animate({"top": "+=20px", "opacity": "toggle"}, defaults.speed);
        }
    });
};

(function($){

    //define the defaults for the plugin and how to call it    
    $.fn.megamenu = function(options){
        //set default options  
        var defaults = {
            classParent: 'dc-mega',
            classContainer: 'sub-container',
            classSubParent: 'mega-hdr',
            classSubLink: 'mega-hdr',
            classWidget: 'dc-extra',
            rowItems: 3,
            speed: 'fast',
            effect: 'fade',
            event: 'hover',
            fullWidth: false,
            onLoad : function(){},
            beforeOpen : function(){},
            beforeClose: function(){},
            openDelay: 100,
            closeDelay: 400
        };

        //call in the default otions
        var options = $.extend(defaults, options);
        var $MegaMenuObj = this;

        //act upon the element that is passed into the design    
        return $MegaMenuObj.each(function(options){

            var clSubParent = defaults.classSubParent;
            var clSubLink = defaults.classSubLink;
            var clParent = defaults.classParent;
            var clContainer = defaults.classContainer;
            var clWidget = defaults.classWidget;
            
            megaSetup();
            
            function megaOver(){
                var subNav = $('.sub',this);
                $(this).addClass('mega-hover');
                if(defaults.effect == 'fade'){
                    $(subNav).fadeIn(defaults.speed);
                }
                if(defaults.effect == 'slide'){
                    $(subNav).show(defaults.speed);
                }
                // beforeOpen callback;
                defaults.beforeOpen.call(this);
            }
            function megaAction(obj){
                var subNav = $('.sub',obj);
                $(obj).addClass('mega-hover');
                if(defaults.effect == 'fade'){
                    $(subNav).fadeIn(defaults.speed);
                }
                if(defaults.effect == 'slide'){
                    $(subNav).show(defaults.speed);
                }
                // beforeOpen callback;
                defaults.beforeOpen.call(this);
            }
            function megaOut(){
                var subNav = $('.sub',this);
                $(this).removeClass('mega-hover');
                $(subNav).hide();
                // beforeClose callback;
                defaults.beforeClose.call(this);
            }
            function megaActionClose(obj){
                var subNav = $('.sub',obj);
                $(obj).removeClass('mega-hover');
                $(subNav).hide();
                // beforeClose callback;
                defaults.beforeClose.call(this);
            }
            function megaReset(){
                $('li',$MegaMenuObj).removeClass('mega-hover');
                $('.sub',$MegaMenuObj).hide();
            }

            function megaSetup(){
                $arrow = '<span class="dc-mega-icon"></span>';
                var clParentLi = clParent+'-li';
                var menuWidth = $MegaMenuObj.outerWidth();
                $('> li',$MegaMenuObj).each(function(){
                    //Set Width of sub
                    var $mainSub = $('> ul',this);
                    var $primaryLink = $('> a',this);
                    if($mainSub.length){
                        $primaryLink.addClass(clParent).append($arrow);
                        $mainSub.addClass('sub').wrap('<div class="'+clContainer+'" />');
                        
                        var pos = $(this).position();
                        pl = pos.left;
                            
                        if($('ul',$mainSub).length){
                            $(this).addClass(clParentLi);
                            $('.'+clContainer,this).addClass('mega');
                            $('> li',$mainSub).each(function(){
                                if(!$(this).hasClass(clWidget)){
                                    $(this).addClass('mega-unit');
                                    if($('> ul',this).length){
                                        $(this).addClass(clSubParent);
                                        $('> a',this).addClass(clSubParent+'-a');
                                    } else {
                                        $(this).addClass(clSubLink);
                                        $('> a',this).addClass(clSubLink+'-a');
                                    }
                                }
                            });

                            // Create Rows
                            var hdrs = $('.mega-unit',this);
                            rowSize = parseInt(defaults.rowItems);
                            for(var i = 0; i < hdrs.length; i+=rowSize){
                                hdrs.slice(i, i+rowSize).wrapAll('<div class="row" />');
                            }

                            // Get Sub Dimensions & Set Row Height
                            $mainSub.show();
                            
                            // Get Position of Parent Item
                            var pw = $(this).width();
                            var pr = pl + pw;
                            
                            // Check available right margin
                            var mr = menuWidth - pr;
                            
                            // // Calc Width of Sub Menu
                            var subw = $mainSub.outerWidth();
                            var totw = $mainSub.parent('.'+clContainer).outerWidth();
                            var cpad = totw - subw;
                            
                            if(defaults.fullWidth == true){
                                var fw = menuWidth - cpad;
                                $mainSub.parent('.'+clContainer).css({width: fw+'px'});
                                $MegaMenuObj.addClass('full-width');
                            }
                            var iw = $('.mega-unit',$mainSub).outerWidth(true);
                            var rowItems = $('.row:eq(0) .mega-unit',$mainSub).length;
                            var inneriw = iw * rowItems;
                            var totiw = inneriw + cpad;
                            
                            // Set mega header height
                            $('.row',this).each(function(){
                                $('.mega-unit:last',this).addClass('last');
                                var maxValue = undefined;
                                $('.mega-unit > a',this).each(function(){
                                    var val = parseInt($(this).height());
                                    if (maxValue === undefined || maxValue < val){
                                        maxValue = val;
                                    }
                                });
                                $('.mega-unit > a',this).css('height',maxValue+'px');
                                $(this).css('width',inneriw+'px');
                            });
                            
                            // Calc Required Left Margin incl additional required for right align
                            
                            if(defaults.fullWidth == true){
                                params = {left: 0};
                            } else {
                                
                                var ml = mr < ml ? ml + ml - mr : (totiw - pw)/2;
                                var subLeft = pl - ml;

                                // If Left Position Is Negative Set To Left Margin
                                var params = {left: pl+'px', marginLeft: -ml+'px'};
                                
                                if(subLeft < 0){
                                    params = {left: 0};
                                }else if(mr < ml){
                                    params = {right: 0};
                                }
                            }
                            $('.'+clContainer,this).css(params);
                            
                            // Calculate Row Height
                            $('.row',$mainSub).each(function(){
                                var rh = $(this).height();
                                $('.mega-unit',this).css({height: rh+'px'});
                                $(this).parent('.row').css({height: rh+'px'});
                            });
                            $mainSub.hide();
                    
                        } else {
                            $('.'+clContainer,this).addClass('non-mega').css('left',pl+'px');
                        }
                    }
                });
                // Set position of mega dropdown to bottom of main menu
                var menuHeight = $('> li > a',$MegaMenuObj).outerHeight(true);
                $('.'+clContainer,$MegaMenuObj).css({top: menuHeight+'px'}).css('z-index','1000');
                
                if(defaults.event == 'hover'){
                    // HoverIntent Configuration
                    var config = {
                        sensitivity: 2,
                        interval: defaults.openDelay,
                        over: megaOver,
                        timeout: defaults.closeDelay,
                        out: megaOut
                    };
                    $('li',$MegaMenuObj).hoverIntent(config);
                }
                
                if(defaults.event == 'click'){
                
                    $('body').mouseup(function(e){
                        if(!$(e.target).parents('.mega-hover').length){
                            megaReset();
                        }
                    });

                    $('> li > a.'+clParent,$MegaMenuObj).click(function(e){
                        var $parentLi = $(this).parent();
                        if($parentLi.hasClass('mega-hover')){
                            megaActionClose($parentLi);
                        } else {
                            megaAction($parentLi);
                        }
                        e.preventDefault();
                    });
                }
                // onLoad callback;
                defaults.onLoad.call(this);
            }
        });
    };
})(jQuery);

/*jslint adsafe: false, bitwise: true, browser: true, cap: false, css: false,
debug: false, devel: true, eqeqeq: true, es5: false, evil: false,
forin: false, fragment: false, immed: true, laxbreak: false, newcap: true,
nomen: false, on: false, onevar: true, passfail: false, plusplus: true,
regexp: false, rhino: true, safe: false, strict: false, sub: false,
undef: true, white: false, widget: false, windows: false */
/*global jQuery: false, window: false */
"use strict";

/*
* Original code (c) 2010 Nick Galbreath
* http://code.google.com/p/stringencoders/source/browse/#svn/trunk/javascript
*
* jQuery port (c) 2010 Carlo Zottmann
* http://github.com/carlo/jquery-base64
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/

/* base64 encode/decode compatible with window.btoa/atob
*
* window.atob/btoa is a Firefox extension to convert binary data (the "b")
* to base64 (ascii, the "a").
*
* It is also found in Safari and Chrome. It is not available in IE.
*
* if (!window.btoa) window.btoa = $.base64.encode
* if (!window.atob) window.atob = $.base64.decode
*
* The original spec's for atob/btoa are a bit lacking
* https://developer.mozilla.org/en/DOM/window.atob
* https://developer.mozilla.org/en/DOM/window.btoa
*
* window.btoa and $.base64.encode takes a string where charCodeAt is [0,255]
* If any character is not [0,255], then an exception is thrown.
*
* window.atob and $.base64.decode take a base64-encoded string
* If the input length is not a multiple of 4, or contains invalid characters
* then an exception is thrown.
*/
 
jQuery.base64 = ( function( $ ) {
  
  var _PADCHAR = "=",
    _ALPHA = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
    _VERSION = "1.0";


  function _getbyte64( s, i ) {
    // This is oddly fast, except on Chrome/V8.
    // Minimal or no improvement in performance by using a
    // object with properties mapping chars to value (eg. 'A': 0)

    var idx = _ALPHA.indexOf( s.charAt( i ) );

    if ( idx === -1 ) {
      throw "Cannot decode base64";
    }

    return idx;
  }
  
  
  function _decode( s ) {
    var pads = 0,
      i,
      b10,
      imax = s.length,
      x = [];

    s = String( s );
    
    if ( imax === 0 ) {
      return s;
    }

    if ( imax % 4 !== 0 ) {
      throw "Cannot decode base64";
    }

    if ( s.charAt( imax - 1 ) === _PADCHAR ) {
      pads = 1;

      if ( s.charAt( imax - 2 ) === _PADCHAR ) {
        pads = 2;
      }

      // either way, we want to ignore this last block
      imax -= 4;
    }

    for ( i = 0; i < imax; i += 4 ) {
      b10 = ( _getbyte64( s, i ) << 18 ) | ( _getbyte64( s, i + 1 ) << 12 ) | ( _getbyte64( s, i + 2 ) << 6 ) | _getbyte64( s, i + 3 );
      x.push( String.fromCharCode( b10 >> 16, ( b10 >> 8 ) & 0xff, b10 & 0xff ) );
    }

    switch ( pads ) {
      case 1:
        b10 = ( _getbyte64( s, i ) << 18 ) | ( _getbyte64( s, i + 1 ) << 12 ) | ( _getbyte64( s, i + 2 ) << 6 );
        x.push( String.fromCharCode( b10 >> 16, ( b10 >> 8 ) & 0xff ) );
        break;

      case 2:
        b10 = ( _getbyte64( s, i ) << 18) | ( _getbyte64( s, i + 1 ) << 12 );
        x.push( String.fromCharCode( b10 >> 16 ) );
        break;
    }

    return x.join( "" );
  }
  
  
  function _getbyte( s, i ) {
    var x = s.charCodeAt( i );

    if ( x > 255 ) {
      throw "INVALID_CHARACTER_ERR: DOM Exception 5";
    }
    
    return x;
  }


  function _encode( s ) {
    if ( arguments.length !== 1 ) {
      throw "SyntaxError: exactly one argument required";
    }

    s = String( s );

    var i,
      b10,
      x = [],
      imax = s.length - s.length % 3;

    if ( s.length === 0 ) {
      return s;
    }

    for ( i = 0; i < imax; i += 3 ) {
      b10 = ( _getbyte( s, i ) << 16 ) | ( _getbyte( s, i + 1 ) << 8 ) | _getbyte( s, i + 2 );
      x.push( _ALPHA.charAt( b10 >> 18 ) );
      x.push( _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) );
      x.push( _ALPHA.charAt( ( b10 >> 6 ) & 0x3f ) );
      x.push( _ALPHA.charAt( b10 & 0x3f ) );
    }

    switch ( s.length - imax ) {
      case 1:
        b10 = _getbyte( s, i ) << 16;
        x.push( _ALPHA.charAt( b10 >> 18 ) + _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) + _PADCHAR + _PADCHAR );
        break;

      case 2:
        b10 = ( _getbyte( s, i ) << 16 ) | ( _getbyte( s, i + 1 ) << 8 );
        x.push( _ALPHA.charAt( b10 >> 18 ) + _ALPHA.charAt( ( b10 >> 12 ) & 0x3F ) + _ALPHA.charAt( ( b10 >> 6 ) & 0x3f ) + _PADCHAR );
        break;
    }

    return x.join( "" );
  }


  return {
    decode: _decode,
    encode: _encode,
    VERSION: _VERSION
  };
      
}( jQuery ) );

;(function($) {

    // TODO rewrite as a widget, removing all the extra plugins
    $.extend($.fn, {
        swapClass: function(c1, c2) {
            var c1Elements = this.filter('.' + c1);
            this.filter('.' + c2).removeClass(c2).addClass(c1);
            c1Elements.removeClass(c1).addClass(c2);
            return this;
        },
        replaceClass: function(c1, c2) {
            return this.filter('.' + c1).removeClass(c1).addClass(c2).end();
        },
        hoverClass: function(className) {
            className = className || "hover";
            return this.hover(function() {
                $(this).addClass(className);
            }, function() {
                $(this).removeClass(className);
            });
        },
        heightToggle: function(animated, callback) {
            animated ?
                this.animate({ height: "toggle" }, animated, callback) :
                this.each(function(){
                    jQuery(this)[ jQuery(this).is(":hidden") ? "show" : "hide" ]();
                    if(callback)
                        callback.apply(this, arguments);
                });
        },
        heightHide: function(animated, callback) {
            if (animated) {
                this.animate({ height: "hide" }, animated, callback);
            } else {
                this.hide();
                if (callback)
                    this.each(callback);                
            }
        },
        prepareBranches: function(settings) {
            if (!settings.prerendered) {
                // mark last tree items
                this.filter(":last-child:not(ul)").addClass(CLASSES.last);
                // collapse whole tree, or only those marked as closed, anyway except those marked as open
                this.filter((settings.collapsed ? "" : "." + CLASSES.closed) + ":not(." + CLASSES.open + ")").find(">ul").hide();
            }
            // return all items with sublists
            return this.filter(":has(>ul)");
        },
        applyClasses: function(settings, toggler) {
            // TODO use event delegation
            this.filter(":has(>ul):not(:has(>a))").find(">span").unbind("click.treeview").bind("click.treeview", function(event) {
                // don't handle click events on children, eg. checkboxes
                if ( this == event.target )
                    toggler.apply($(this).next());
            }).add( $("a", this) ).hoverClass();
            
            if (!settings.prerendered) {
                // handle closed ones first
                this.filter(":has(>ul:hidden)")
                        .addClass(CLASSES.expandable)
                        .replaceClass(CLASSES.last, CLASSES.lastExpandable);
                        
                // handle open ones
                this.not(":has(>ul:hidden)")
                        .addClass(CLASSES.collapsable)
                        .replaceClass(CLASSES.last, CLASSES.lastCollapsable);
                        
                // create hitarea if not present
                var hitarea = this.find("div." + CLASSES.hitarea);
                if (!hitarea.length)
                    hitarea = this.prepend("<div class=\"" + CLASSES.hitarea + "\"/>").find("div." + CLASSES.hitarea);
                hitarea.removeClass().addClass(CLASSES.hitarea).each(function() {
                    var classes = "";
                    $.each($(this).parent().attr("class").split(" "), function() {
                        classes += this + "-hitarea ";
                    });
                    $(this).addClass( classes );
                })
            }
            
            // apply event to hitarea
            this.find("div." + CLASSES.hitarea).click( toggler );
        },
        treeview: function(settings) {
            
            settings = $.extend({
                cookieId: "treeview"
            }, settings);
            
            if ( settings.toggle ) {
                var callback = settings.toggle;
                settings.toggle = function() {
                    return callback.apply($(this).parent()[0], arguments);
                };
            }
        
            // factory for treecontroller
            function treeController(tree, control) {
                // factory for click handlers
                function handler(filter) {
                    return function() {
                        // reuse toggle event handler, applying the elements to toggle
                        // start searching for all hitareas
                        toggler.apply( $("div." + CLASSES.hitarea, tree).filter(function() {
                            // for plain toggle, no filter is provided, otherwise we need to check the parent element
                            return filter ? $(this).parent("." + filter).length : true;
                        }) );
                        return false;
                    };
                }
                // click on first element to collapse tree
                $("a:eq(0)", control).click( handler(CLASSES.collapsable) );
                // click on second to expand tree
                $("a:eq(1)", control).click( handler(CLASSES.expandable) );
                // click on third to toggle tree
                $("a:eq(2)", control).click( handler() ); 
            }
        
            // handle toggle event
            function toggler() {
                $(this)
                    .parent()
                    // swap classes for hitarea
                    .find(">.hitarea")
                        .swapClass( CLASSES.collapsableHitarea, CLASSES.expandableHitarea )
                        .swapClass( CLASSES.lastCollapsableHitarea, CLASSES.lastExpandableHitarea )
                    .end()
                    // swap classes for parent li
                    .swapClass( CLASSES.collapsable, CLASSES.expandable )
                    .swapClass( CLASSES.lastCollapsable, CLASSES.lastExpandable )
                    // find child lists
                    .find( ">ul" )
                    // toggle them
                    .heightToggle( settings.animated, settings.toggle );
                if ( settings.unique ) {
                    $(this).parent()
                        .siblings()
                        // swap classes for hitarea
                        .find(">.hitarea")
                            .replaceClass( CLASSES.collapsableHitarea, CLASSES.expandableHitarea )
                            .replaceClass( CLASSES.lastCollapsableHitarea, CLASSES.lastExpandableHitarea )
                        .end()
                        .replaceClass( CLASSES.collapsable, CLASSES.expandable )
                        .replaceClass( CLASSES.lastCollapsable, CLASSES.lastExpandable )
                        .find( ">ul" )
                        .heightHide( settings.animated, settings.toggle );
                }
            }
            this.data("toggler", toggler);
            
            function serialize() {
                function binary(arg) {
                    return arg ? 1 : 0;
                }
                var data = [];
                branches.each(function(i, e) {
                    data[i] = $(e).is(":has(>ul:visible)") ? 1 : 0;
                });
                $.cookie(settings.cookieId, data.join(""), settings.cookieOptions );
            }
            
            function deserialize() {
                var stored = $.cookie(settings.cookieId);
                if ( stored ) {
                    var data = stored.split("");
                    branches.each(function(i, e) {
                        $(e).find(">ul")[ parseInt(data[i]) ? "show" : "hide" ]();
                    });
                }
            }
            
            // add treeview class to activate styles
            this.addClass("treeview");
            
            // prepare branches and find all tree items with child lists
            var branches = this.find("li").prepareBranches(settings);
            
            switch(settings.persist) {
            case "cookie":
                var toggleCallback = settings.toggle;
                settings.toggle = function() {
                    serialize();
                    if (toggleCallback) {
                        toggleCallback.apply(this, arguments);
                    }
                };
                deserialize();
                break;
            case "location":
                var current = this.find("a").filter(function() {
                    return this.href.toLowerCase() == location.href.toLowerCase();
                });
                if ( current.length ) {
                    // TODO update the open/closed classes
                    var items = current.addClass("selected").parents("ul, li").add( current.next() ).show();
                    if (settings.prerendered) {
                        // if prerendered is on, replicate the basic class swapping
                        items.filter("li")
                            .swapClass( CLASSES.collapsable, CLASSES.expandable )
                            .swapClass( CLASSES.lastCollapsable, CLASSES.lastExpandable )
                            .find(">.hitarea")
                                .swapClass( CLASSES.collapsableHitarea, CLASSES.expandableHitarea )
                                .swapClass( CLASSES.lastCollapsableHitarea, CLASSES.lastExpandableHitarea );
                    }
                }
                break;
            }
            
            branches.applyClasses(settings, toggler);
                
            // if control option is set, create the treecontroller and show it
            if ( settings.control ) {
                treeController(this, settings.control);
                $(settings.control).show();
            }
            
            return this;
        }
    });
    
    // classes used by the plugin
    // need to be styled via external stylesheet, see first example
    $.treeview = {};
    var CLASSES = ($.treeview.classes = {
        open: "open",
        closed: "closed",
        expandable: "expandable",
        expandableHitarea: "expandable-hitarea",
        lastExpandableHitarea: "lastExpandable-hitarea",
        collapsable: "collapsable",
        collapsableHitarea: "collapsable-hitarea",
        lastCollapsableHitarea: "lastCollapsable-hitarea",
        lastCollapsable: "lastCollapsable",
        lastExpandable: "lastExpandable",
        last: "last",
        hitarea: "hitarea"
    });
    
})(jQuery);