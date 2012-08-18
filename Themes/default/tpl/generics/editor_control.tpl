{function control_richedit}
{$editor_context = $C.controls.richedit.$editor_id}
<div>
  <div>
    <div>
      <div id="smileyBox_message" class="blue_container"></div>
          <textarea class="editor" name="{$editor_id}" id="{$editor_id}" rows="{$editor_context.rows}" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="{$C.tabindex}" style="max-width:100%; width:99%; height: {$editor_context.height}; {(isset($C.post_error.no_message) or isset($C.post_error.long_message)) ? 'border: 1px solid red;' : ''}">{$editor_context.value}</textarea>
        </div>
        {*<div id="{$editor_id}_resizer" class="richedit_resize"></div>*}
      </div>
    </div>
    <input type="hidden" name="{$editor_id}_mode" id="{$editor_id}_mode" value="0" />
    <script type="text/javascript"><!-- // --><![CDATA[
      // Show the smileys.
      {if (!empty($C.smileys.postform) or !empty($C.smileys.popup)) and !$editor_context.disable_smiley_box and $smileyContainer !== null}
          var oSmileyBox_{$editor_id} = new smc_SmileyBox( {
          sUniqueId: {$SUPPORT->JavaScriptEscape('smileyBox_'|cat:$editor_id)},
          sContainerDiv: {$SUPPORT->JavaScriptEscape($smileyContainer)},
          sClickHandler: {$SUPPORT->JavaScriptEscape('oEditorHandle_'|cat:$editor_id|cat:'.insertSmiley')},
          oSmileyLocations: {
            postform:
            [ 

        {foreach $C.smileys as $smileyRows}
          {foreach $smileyRows as $smileyRow}
            {foreach $smileyRow.smileys as $smiley}
                {
                    sCode: {$SUPPORT->JavaScriptEscape($smiley.code)},
                    sSrc: {$SUPPORT->JavaScriptEscape($S.smileys_url|cat:'/'|cat:$smiley.filename)},
                    sDescription: {$SUPPORT->JavaScriptEscape($smiley.description)}
                }
                {(empty($smiley.isLast)) ? ',' : ''}
            {/foreach}
          {/foreach}
        {/foreach}
              ]
            },
            sSmileyBoxTemplate: {$SUPPORT->JavaScriptEscape('%smileyRows%')},
            sSmileyRowTemplate: {$SUPPORT->JavaScriptEscape('<div>%smileyRow%</div>')},
            sSmileyTemplate: {$SUPPORT->JavaScriptEscape('<div class="smiley_wrapper"><img style="margin:auto;" src="%smileySource%" alt="%smileyDescription%" title="%smileyDescription%" id="%smileyId%" /></div>')},
            sMoreSmileysTemplate: {$SUPPORT->JavaScriptEscape('<a href="#" id="%moreSmileysId%"></a>')}
            } );
      {/if}

    {if $C.show_bbc and $bbcContainer !== null}
        var oBBCBox_{$editor_id} = new smc_BBCButtonBox( {
          sUniqueId: {$SUPPORT->JavaScriptEscape('BBCBox_'|cat:$editor_id)},
          sContainerDiv: {$SUPPORT->JavaScriptEscape($bbcContainer)},
          sButtonClickHandler: {$SUPPORT->JavaScriptEscape('oEditorHandle_'|cat:$editor_id|cat:'.handleButtonClick')},
          sSelectChangeHandler: {$SUPPORT->JavaScriptEscape('oEditorHandle_'|cat:$editor_id|cat:'.handleSelectChange')},
          aButtonRows: [
      {foreach $C.bbc_tags as $i => $buttonRow}
        [
        {foreach $buttonRow as $tag}
          {if isset($tag.before)}
              {$code = $tag.code}
              {
                sType: 'button',
                bEnabled: {(empty($C.disabled_tags.$code)) ? 'true' : 'false'},
                sImage: {$SUPPORT->JavaScriptEscape($S.images_url|cat:'/bbc/'|cat:$tag.image|cat:'.png')},
                sCode: {$SUPPORT->JavaScriptEscape($tag.code)},
                sBefore: {$SUPPORT->JavaScriptEscape($tag.before)},
                sAfter: {(isset($tag.after)) ? $SUPPORT->JavaScriptEscape($tag.after) : 'null'},
                sDescription: {$SUPPORT->JavaScriptEscape($tag.description)}
              } {(empty($tag.isLast)) ? ',' : ''}
          {else}
              {
                sType: 'divider'
              } {(empty($tag.isLast)) ? ',' : ''}
          {/if}
        {/foreach}
        {if $i == 0}
          {if !isset($C.disabled_tags.font)}
            ,
              {
                sType: 'select',
                sName: 'sel_face',
                oOptions: {
                  '': {$SUPPORT->JavaScriptEscape($T.font_face)},
                  'consolas,monospaced': 'Monospaced',
                  'arial': 'Arial',
                  'arial black': 'Arial Black',
                  'impact': 'Impact',
                  'verdana': 'Verdana',
                  'times new roman': 'Times New Roman',
                  'georgia': 'Georgia',
                  'trebuchet ms': 'Trebuchet MS',
                  'comic sans ms': 'Comic Sans MS'
                }
              }
          {/if}  
          {if !isset($C.disabled_tags.size)}
            ,
              {
                sType: 'select',
                sName: 'sel_size',
                oOptions: {
                  '': {$SUPPORT->JavaScriptEscape($T.font_size)},
                  '1': '8pt',
                  '2': '10pt',
                  '3': '12pt',
                  '4': '14pt',
                  '5': '18pt',
                  '6': '24pt',
                  '7': '36pt'
                }
              }
          {/if}
          {if !isset($C.disabled_tags.color)}
            ,
              {
                sType: 'select',
                sName: 'sel_color',
                oOptions: {
                  '': {$SUPPORT->JavaScriptEscape($T.change_color)},
                  'black': {$SUPPORT->JavaScriptEscape($T.black)},
                  'red': {$SUPPORT->JavaScriptEscape($T.red)},
                  'yellow': {$SUPPORT->JavaScriptEscape($T.yellow)},
                  'pink': {$SUPPORT->JavaScriptEscape($T.pink)},
                  'green': {$SUPPORT->JavaScriptEscape($T.green)},
                  'orange': {$SUPPORT->JavaScriptEscape($T.orange)},
                  'purple': {$SUPPORT->JavaScriptEscape($T.purple)},
                  'blue': {$SUPPORT->JavaScriptEscape($T.blue)},
                  'beige': {$SUPPORT->JavaScriptEscape($T.beige)},
                  'brown': {$SUPPORT->JavaScriptEscape($T.brown)},
                  'teal': {$SUPPORT->JavaScriptEscape($T.teal)},
                  'navy': {$SUPPORT->JavaScriptEscape($T.navy)},
                  'maroon': {$SUPPORT->JavaScriptEscape($T.maroon)},
                  'limegreen': {$SUPPORT->JavaScriptEscape($T.lime_green)},
                  'white': {$SUPPORT->JavaScriptEscape($T.white)}
                }
              }
          {/if}
        {/if}
        ] {($i == count($C.bbc_tags) - 1) ? '' : ','}
      {/foreach}
      ],
          sButtonTemplate: {$SUPPORT->JavaScriptEscape('<img id="%buttonId%" src="%buttonSrc%" class="bbc_editor_button" alt="%buttonDescription%" title="%buttonDescription%" />')},
          sButtonBackgroundImage: {$SUPPORT->JavaScriptEscape('')},
          sButtonBackgroundImageHover: {$SUPPORT->JavaScriptEscape('')},
          sActiveButtonBackgroundImage: {$SUPPORT->JavaScriptEscape('')},
          sDividerTemplate: {$SUPPORT->JavaScriptEscape('<img src="'|cat:$S.images_url|cat:'/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" />')},
          sSelectTemplate: {$SUPPORT->JavaScriptEscape('<select name="%selectName%" id="%selectId%" style="margin-bottom: 1ex; font-size: x-small;">%selectOptions%</select>')},
          sButtonRowTemplate: {$SUPPORT->JavaScriptEscape('<div>%buttonRow%</div>')}
        } );
    {/if}
        var oEditorHandle_{$editor_id} = new smc_Editor( {
          sSessionId: {$SUPPORT->JavaScriptEscape($C.session_id)},
          sSessionVar: {$SUPPORT->JavaScriptEscape($C.session_var)},
          sFormId: {$SUPPORT->JavaScriptEscape($editor_context.form)},
          sUniqueId: {$SUPPORT->JavaScriptEscape($editor_id)},
          bRTL: {($T.lang_rtl) ? 'true' : 'false'},
          bWysiwyg: {($editor_context.rich_active) ? 'true' : 'false'},
          sText: {$SUPPORT->JavaScriptEscape(($editor_context.rich_active) ? $editor_context.rich_value : '')},
          sEditWidth: {$SUPPORT->JavaScriptEscape($editor_context.width)},
          sEditHeight: {$SUPPORT->JavaScriptEscape($editor_context.height)},
          bRichEditOff: {(empty($M.disable_wysiwyg)) ? 'false' : 'true'},
          oSmileyBox: {(!empty($C.smileys.postform) and !$editor_context.disable_smiley_box and $smileyContainer !== null) ? ('oSmileyBox_'|cat:$editor_id) : 'null'},
          oBBCBox: {($C.show_bbc and $bbcContainer !== null) ? ('oBBCBox_'|cat:$editor_id) : 'null'}
        } );
        smf_editorArray[smf_editorArray.length] = oEditorHandle_{$editor_id};
      // ]]></script>
{/function}

{function control_richedit_buttons}
  {$editor_context = $C.controls.richedit.$editor_id}
  <input type="submit" value="{(isset($editor_context.labels.post_button)) ? $editor_context.labels.post_button : $T.post}" tabindex="{$C.tabindex}" onclick="return submitThisOnce(this);" accesskey="s" class="default" />
  {$C.tabindex = $C.tabindex+1}
  {if $editor_context.preview_type}
    <input type="submit" name="preview" value="{(isset($editor_context.labels.preview_button)) ? $editor_context.labels.preview_button : $T.preview}" tabindex="{$C.tabindex}" onclick="{($editor_context.preview_type == 2) ? 'return event.ctrlKey || previewPost();' : 'return submitThisOnce(this);'}" accesskey="p" class="button_submit" />
  {/if}
  {$SUPPORT->displayHook('editor_buttons_extend')}
  {if !empty($C.can_save_draft)}
    <input type="hidden" id="draft_id" name="draft_id" value="{(empty($C.draft_id)) ? '0' : $C.draft_id}" />
    <input type="submit" name="draft" value="{$T.save_draft}" tabindex="{$C.tabindex}" onclick="return submitThisOnce(this);" accesskey="d" class="button_submit" />
  {/if}
  {if !empty($C.can_autosave_draft)}
    {*<span id="draft_lastautosave" class="clear righttext" style="display: block;"></span>
    $context['inline_footer_script'] .= '
  var oAutoSave = new draftAutosave({
    sSelf: \'oAutoSave\',
    sScriptUrl: smf_scripturl,
    sSessionId: \''. $context['session_id']. '\',
    sSessionVar: \''. $context['session_var']. '\',
    sLastNote: \'draft_lastautosave\',
    sType: \'full\',
    iBoard: '.(empty($context['current_board']) ? 0 : $context['current_board']). ',
    iFreq: '.(empty($modSettings['enableAutoSaveDrafts']) ? 30000 : $modSettings['enableAutoSaveDrafts'] * 1000).'
  });
  ';*}
  {/if}
{/function}
