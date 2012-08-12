<dt>
  <strong>{$T.signature}:</strong><br>
  <span class="smalltext">{$T.sig_info}</span><br>
  <br>
</dt>
<dd>
  <textarea class="editor" onkeyup="calcCharLeft();" name="signature" rows="5" cols="50">{$C.member.signature}</textarea><br>
  {if !empty($C.signature_limits.max_length)}
    <span class="smalltext">{$T.max_sig_characters|sprintf:$C.signature_limits.max_length}<span id="signatureLeft">{$C.signature_limits.max_length}</span></span><br>
  {/if}
  {if $C.signature_warning}
    <span class="smalltext">{$C.signature_warning}</span>
  {/if}
  <script type="text/javascript"><!-- // --><![CDATA[
    function tick()
    {
      if (typeof(document.forms.creator) != "undefined")
      {
        calcCharLeft();
        setTimeout("tick()", 1000);
      }
      else
        setTimeout("tick()", 800);
    }

    function calcCharLeft()
    {
      var maxLength = {$C.signature_limits.max_length};
      var oldSignature = "", currentSignature = document.forms.creator.signature.value;

      if (!document.getElementById("signatureLeft"))
        return;

      if (oldSignature != currentSignature)
      {
        oldSignature = currentSignature;

        if (currentSignature.replace(/\r/, "").length > maxLength)
          document.forms.creator.signature.value = currentSignature.replace(/\r/, "").substring(0, maxLength);
        currentSignature = document.forms.creator.signature.value.replace(/\r/, "");
      }

      setInnerHTML(document.getElementById("signatureLeft"), maxLength - currentSignature.length);
    }
    addLoadEvent(tick);
  // ]]>
  </script>
</dd>
