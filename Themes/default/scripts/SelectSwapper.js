/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://code.mattzuba.com code.
 *
 * The Initial Developer of the Original Code is
 * Matt Zuba.
 * Portions created by the Initial Developer are Copyright (C) 2010-2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * ***** END LICENSE BLOCK ***** */

/**
 * We won't get the state of any items, we'll update form fields realtime
 */
function SelectSwapper(oOptions)
{
    this.oOpts = oOptions;
    this.bSortOnMove = this.oOpts.bSortOnMove !== undefined && this.oOpts.bSortOnMove == false ? false : true;

    this.init();
}

SelectSwapper.prototype.init = function()
{
    // Setup our objects
    this.oFromBox = document.getElementById(this.oOpts.sFromBoxId);
    this.oFromBoxHidden = this.oOpts.sFromBoxHiddenId != undefined ? document.getElementById(this.oOpts.sFromBoxHiddenId) : null;
    this.oToBox = document.getElementById(this.oOpts.sToBoxId);
    this.oToBoxHidden = this.oOpts.sToBoxHiddenId != undefined ? document.getElementById(this.oOpts.sToBoxHiddenId) : null;
    this.oAddButton = document.getElementById(this.oOpts.sAddButtonId);
    this.oAddAllButton = document.getElementById(this.oOpts.sAddAllButtonId);
    this.oRemoveButton = document.getElementById(this.oOpts.sRemoveButtonId);
    this.oRemoveAllButton = document.getElementById(this.oOpts.sRemoveAllButtonId);

    // Sort them initially if needed
    if (this.bSortOnMove)
    {
        this.Sort(this.oFromBox);
        this.Sort(this.oToBox);
    }

    // Add some listeners to the buttons
    var swap = this;
    this.oAddButton.onclick = function(){
        swap.Move(swap.oFromBox, swap.oToBox, false);
    };

    this.oAddAllButton.onclick = function(){
        swap.Move(swap.oFromBox, swap.oToBox, true);
    };

    this.oRemoveButton.onclick = function(){
        swap.Move(swap.oToBox, swap.oFromBox, false);
    };

    this.oRemoveAllButton.onclick = function(){
        swap.Move(swap.oToBox, swap.oFromBox, true);
    };
}

SelectSwapper.prototype.Move = function(oOrigin, oDestination, bMoveAll)
{
    var aRemoves = [];
    for (var i = 0; i < oOrigin.length; i++)
    {
        if (oOrigin.options[i].selected || bMoveAll)
        {
            var oOption = document.createElement('option');
            oOption.value = oOrigin.options[i].value;
            oOption.text = oOrigin.options[i].text;
            try {
                oDestination.add(oOption, null);
            }
            catch (e) {
                oDestination.add(oOption);
            }
            aRemoves.push(i);
        }
    }

    // If we didn't move anything, no point in continuing
    if (aRemoves.length == 0)
        return;

    aRemoves.reverse();
    for (i in aRemoves)
        oOrigin.options[aRemoves[i]] = null;

    if (this.bSortOnMove)
        this.Sort(oDestination);

    this.updateHidden();
}

SelectSwapper.prototype.Sort = function(oSelectBox)
{
    // Read the box items into an array
    var aItems = [];

    for (var i = 0; i < oSelectBox.length; i++)
        aItems[i] = {
            "sValue" : oSelectBox.options[i].value, 
            "sText": oSelectBox.options[i].text
            };

    aItems.sort(function(a,b)
    {
        var sTextA = a.sText.toLowerCase(), sTextB = b.sText.toLowerCase();
        return (sTextA < sTextB) ? -1 : (sTextA > sTextB ? 1 : 0);
    });

    oSelectBox.options.length = 0;
    for (i = 0; i < aItems.length; i++)
    {
        var oOption = document.createElement('option');
        oOption.value = aItems[i].sValue;
        oOption.text = aItems[i].sText;
        oSelectBox.options.add(oOption);
    }
}

SelectSwapper.prototype.updateHidden = function()
{
    if (this.oToBoxHidden)
    {
        var aPieces = [];
        for (var i = 0; i < this.oToBox.options.length; i++)
            aPieces.push(this.oToBox.options[i].value);
        this.oToBoxHidden.value = aPieces.join(',');
    }
    if (this.oFromBoxHidden)
    {
        aPieces = [];
        for (i = 0; i < this.oToBox.options.length; i++)
            aPieces.push(this.oToBox.options[i].value);
        this.oFromBoxHidden.value = aPieces.join(',');
    }
}