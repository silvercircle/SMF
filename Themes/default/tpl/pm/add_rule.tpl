{$PMCONTEXT->rulesJS()}
<form action="{$SCRIPTURL}?action=pm;sa=manrules;save;rid={$C.rid}" method="post" accept-charset="UTF-8" name="addrule" id="addrule" class="flow_hidden">
  <div class="cat_bar2">
    <h3>{($C.rid == 0) ? $T.pm_add_rule : $T.pm_edit_rule}</h3>
  </div>
  <div class="blue_container cleantop">
    <div class="content">
      <dl class="addrules">
        <dt class="floatleft">
          <strong>{$T.pm_rule_name}:</strong><br>
          <span class="smalltext">{$T.pm_rule_name_desc}</span>
        </dt>
        <dd class="floatleft">
          <input type="text" name="rule_name" value="{(empty($C.rule.name)) ? $T.pm_rule_name_default : $C.rule.name}" size="50" class="input_text" />
        </dd>
        </dl>
        <fieldset>
          <legend>{$T.pm_rule_criteria}</legend>
          {$isFirst = true}
          {foreach $C.rule.criteria as $k => $criteria}
            {if !$isFirst and $criteria.t == ''}
              <div id="removeonjs1">
            {elseif !$isFirst}
              <br>
            {/if}
            <select name="ruletype[{$k}]" id="ruletype{$k}" onchange="updateRuleDef({$k}); rebuildRuleDesc();">
              <option value="">{$T.pm_rule_criteria_pick}:</option>
              <option value="mid" {($criteria.t == 'mid') ? 'selected="selected"' : ''}>{$T.pm_rule_mid}</option>
              <option value="gid" {($criteria.t == 'gid') ? 'selected="selected"' : ''}>{$T.pm_rule_gid}</option>
              <option value="sub" {($criteria.t == 'sub') ? 'selected="selected"' : ''}>{$T.pm_rule_sub}</option>
              <option value="msg" {($criteria.t == 'msg') ? 'selected="selected"' : ''}>{$T.pm_rule_msg}</option>
              <option value="bud" {($criteria.t == 'bud') ? 'selected="selected"' : ''}>{$T.pm_rule_bud}</option>
            </select>
            <span id="defdiv{$k}" {(!in_array($criteria.t, array('gid', 'bud'))) ? '' : 'style="display: none;"'}>
              <input type="text" name="ruledef[{$k}]" id="ruledef{$k}" onkeyup="rebuildRuleDesc();" value="{(in_array($criteria.t, array('mid', 'sub', 'msg'))) ? $criteria.v : ''}" class="input_text" />
            </span>
            <span id="defseldiv{$k}" {($criteria.t == 'gid') ? '' : 'style="display: none;"'}>
            <select name="ruledefgroup[{$k}]" id="ruledefgroup{$k}" onchange="rebuildRuleDesc();">
              <option value="">{$T.pm_rule_sel_group}</option>
              {foreach $C.groups as $id => $group}
                <option value="{$id}" {($criteria.t == 'gid' and $criteria.v == $id) ? 'selected="selected"' : ''}>{$group}</option>
              {/foreach}
            </select>
            </span>
            {if $isFirst}
              {$isFirst = false}
            {elseif $criteria.t == ''}
              </div>
            {/if}
          {/foreach}
          <span id="criteriaAddHere"></span>
          <br>
          <a href="#" onclick="addCriteriaOption(); return false;" id="addonjs1" style="display: none;">({$T.pm_rule_criteria_add})</a>
          <br><br>
          {$T.pm_rule_logic}:
          <select name="rule_logic" id="logic" onchange="rebuildRuleDesc();">
            <option value="and" {($C.rule.logic == 'and') ? 'selected="selected"' : ''}>{$T.pm_rule_logic_and}</option>
            <option value="or" {($C.rule.logic == 'or') ? 'selected="selected"' : ''}>{$T.pm_rule_logic_or}</option>
          </select>
        </fieldset>
        <fieldset>
          <legend>{$T.pm_rule_actions}</legend>
          {$isFirst = true}
          {foreach $C.rule.actions as $k => $action}
            {if !$isFirst and $action.t == ''}
              <div id="removeonjs2">
            {elseif !$isFirst}
              <br>
            {/if}
            <select name="acttype[{$k}]" id="acttype{$k}" onchange="updateActionDef({$k}); rebuildRuleDesc();">
              <option value="">{$T.pm_rule_sel_action}:</option>
              <option value="lab"{($action.t == 'lab') ? ' selected="selected"' : ''}>{$T.pm_rule_label}</option>
              <option value="del"{($action.t == 'del') ? ' selected="selected"' : ''}>{$T.pm_rule_delete}</option>
            </select>
            <span id="labdiv{$k}">
            <select name="labdef[{$k}]" id="labdef{$k}" onchange="rebuildRuleDesc();">
              <option value="">{$T.pm_rule_sel_label}</option>
              {foreach $C.labels as $label}
                {if $label.id != -1}
                  <option value="{$label.id + 1}"{($action.t == 'lab' and $action.v == $label.id) ? ' selected="selected"' : ''}>{$label.name}</option>
                {/if}
              {/foreach}
            </select>
            </span>
            {if $isFirst}
              {$isFirst = false}
            {elseif $action.t == ''}
              </div>
            {/if}
          {/foreach}
          <span id="actionAddHere"></span><br>
          <a href="#" onclick="addActionOption(); return false;" id="addonjs2" style="display: none;">({$T.pm_rule_add_action})</a>
        </fieldset>
      </div>
    </div><br class="clear">
    <div class="cat_bar">
      <h3>{$T.pm_rule_description}</h3>
    </div>
    <div class="blue_container cleantop mediumpadding">
      <div id="ruletext">{$T.pm_rule_js_disabled}</div>
    </div>
    <br>
    <div class="righttext smallpadding">
      {$C.hidden_sid_input}
      <input type="submit" name="save" value="{$T.pm_rule_save}" class="default" />
    </div>
  </form>
  <script type="text/javascript"><!-- // --><![CDATA[';
  {foreach $C.rule.criteria as $k => $c}
      updateRuleDef({$k});
  {/foreach}
  {foreach $C.rule.actions as $k => $c}
      updateActionDef({$k});
  {/foreach}
      rebuildRuleDesc();

  {if $C.rid}
      document.getElementById("removeonjs1").style.display = "none";
      document.getElementById("removeonjs2").style.display = "none";
  {/if}

      document.getElementById("addonjs1").style.display = "";
      document.getElementById("addonjs2").style.display = "";
    // ]]></script>

